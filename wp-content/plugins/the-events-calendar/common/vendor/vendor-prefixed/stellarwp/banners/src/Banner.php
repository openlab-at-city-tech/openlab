<?php declare(strict_types=1);

namespace TEC\Common\Nexcess\Banners;

use DateTimeImmutable;

/**
 * One marketing banner, printed wherever the client calls render().
 */
final class Banner {

	/**
	 * Prefix of the shared action fired when a banner renders. Every plugin bundles its own copy of
	 * this package, so the first copy to render an ID on a request fires it and later copies of the
	 * same ID stand down.
	 */
	private const RENDERED_ACTION = 'nx_banners_rendered:';

	/**
	 * Prefix of the filter on whether a banner is in its schedule. Shared across copies, so it applies
	 * whichever copy renders the ID.
	 */
	private const SCHEDULED_FILTER = 'nx_banners_scheduled:';

	/**
	 * Prefix of the handle a banner's stylesheet registers under.
	 */
	private const STYLE_HANDLE = 'nx-banners-';

	/**
	 * The hooks core prints notices on. wp-admin's common.js moves a notice printed there under the
	 * page heading, unless it carries "inline".
	 */
	private const NOTICE_HOOKS = [ 'admin_notices', 'all_admin_notices', 'network_admin_notices', 'user_admin_notices' ];

	private string $id;

	private string $version;

	private BannerFields $fields;

	private string $packageUrl;

	private ?DateTimeImmutable $start;

	private ?DateTimeImmutable $end;

	/**
	 * @param string             $id      Shared across plugins: the same ID is the same banner and the same dismissal.
	 * @param string             $version Compared with version_compare(): the registry keeps the newest version of an ID.
	 * @param BannerFields       $fields     The banner fields.
	 * @param string             $packageUrl Where the registering plugin serves its copy of this package, such as
	 *                                       plugins_url( 'vendor-prefixed/stellarwp/banners/', MY_PLUGIN_FILE ).
	 * @param ?DateTimeImmutable $start      The banner shows from this moment. Null shows it from now.
	 * @param ?DateTimeImmutable $end        The banner stops showing at this moment. Null never ends it.
	 */
	public function __construct(
		string $id,
		string $version,
		BannerFields $fields,
		string $packageUrl,
		?DateTimeImmutable $start = null,
		?DateTimeImmutable $end = null
	) {
		$this->id         = $id;
		$this->version    = $version;
		$this->fields     = $fields;
		$this->packageUrl = $packageUrl;
		$this->start      = $start;
		$this->end        = $end;
	}

	public function id(): string {
		return $this->id;
	}

	public function version(): string {
		return $this->version;
	}

	/**
	 * Edit these to change what this banner shows, such as to set translated copy.
	 */
	public function fields(): BannerFields {
		return $this->fields;
	}

	/**
	 * Print the banner here unless this is not wp-admin, it is already rendered on this request, out
	 * of schedule or dismissed, or the callable declines.
	 *
	 * @param (callable(): bool)|null $shouldDisplay Omit to render unconditionally.
	 * @param ?string                 $template      Path of a template file to render instead of the bundled one.
	 * @param ?string                 $stylesheet    URL of a stylesheet to print instead of the bundled one.
	 */
	public function render( ?callable $shouldDisplay = null, ?string $template = null, ?string $stylesheet = null ): void {
		Dismissals::handleRequest( $this->id );

		if (
			! is_admin()
			|| did_action( self::RENDERED_ACTION . $this->id ) > 0
			|| ! $this->isScheduled()
			|| Dismissals::isDismissed( $this->id )
			|| ( $shouldDisplay !== null && ! $shouldDisplay() )
		) {
			return;
		}

		do_action( self::RENDERED_ACTION . $this->id );

		// Let a banner on a notice hook move under the heading like any notice, and keep one rendered
		// anywhere else where the client called render().
		$inline = array_filter( self::NOTICE_HOOKS, 'doing_action' ) === [];

		$this->printStylesheet( $stylesheet );
		$this->printTemplate( $template, $this->fields, Dismissals::url( $this->id ), $inline );
	}

	/**
	 * Print the stylesheet's link right here. render() runs mid-page, after wp-admin printed the
	 * head's styles, so an enqueued one would print in the footer and leave the banner unstyled until then.
	 */
	private function printStylesheet( ?string $stylesheet ): void {
		wp_register_style(
			self::STYLE_HANDLE . $this->id,
			$stylesheet ?? rtrim( $this->packageUrl, '/' ) . '/src/templates/banner.css',
			[],
			$stylesheet === null ? Package::VERSION : null
		);
		wp_print_styles( self::STYLE_HANDLE . $this->id );
	}

	/**
	 * Print the given template, or the bundled one, with $fields, $dismiss_url and $inline in scope.
	 * They are snake_case so that templates pass the WordPress coding standards most plugins lint with.
	 */
	private function printTemplate( ?string $template, BannerFields $fields, string $dismiss_url, bool $inline ): void {
		// Every other copy has already stood down for this ID, so a broken path must not hide it.
		if ( $template === null || ! is_file( $template ) ) {
			$template = __DIR__ . '/templates/banner.php';
		}

		require $template;
	}

	private function isScheduled(): bool {
		$now = time();

		$scheduled = ( $this->start === null || $now >= $this->start->getTimestamp() )
			&& ( $this->end === null || $now < $this->end->getTimestamp() );

		// Lets QA show a banner outside its schedule, or hide one inside it, without moving the clock.
		return (bool) apply_filters( self::SCHEDULED_FILTER . $this->id, $scheduled );
	}

	/**
	 * A copy edits its own fields, never the original's.
	 */
	public function __clone() {
		$this->fields = clone $this->fields;
	}

}
