<?php
namespace Bookly\Frontend\Modules\MobileStaffCabinet\Api\Handlers;

use Bookly\Lib;
use Bookly\Lib\Entities\Staff;
use Bookly\Frontend\Modules\MobileStaffCabinet\Api;

abstract class Handler implements HandlerInterface
{
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_STAFF = 'staff';

    /** @var string */
    protected $role;
    /** @var Staff */
    protected $staff;
    /** @var \WP_User */
    protected $wp_user;
    /** @var Lib\Utils\Collection */
    protected $params;
    /** @var string */
    protected $resolver_method_name = '';
    /** @var Lib\Base\Request */
    protected $request;

    /**
     * @param \WP_User | Staff $staff_or_wp_user
     * @param string $resolver
     */
    public function __construct( $staff_or_wp_user, $resolver )
    {
        if ( $staff_or_wp_user instanceof \WP_User ) {
            $this->role = self::ROLE_SUPERVISOR;
            $this->wp_user = $staff_or_wp_user;
            Lib\Utils\Log::setAuthor( $staff_or_wp_user->display_name );
        } elseif ( $staff_or_wp_user instanceof Staff ) {
            $this->role = self::ROLE_STAFF;
            $this->staff = $staff_or_wp_user;
            $this->staff && Lib\Utils\Log::setAuthor( $staff_or_wp_user->getFullName() );
        }

        $this->resolver_method_name = $resolver;
    }

    /**
     * @param Lib\Base\Request $request
     * @return Api\Response
     * @throws Api\Exceptions\ParameterException
     */
    public function __invoke( Lib\Base\Request $request )
    {
        $this->request = $request;
        $this->params = new Lib\Utils\Collection( $request->get( 'params', array() ) );

        $data = $this->{$this->resolver_method_name}();
        $response = new Api\Response( array( 'result' => $data ) );

        return $response->addHeader( 'X-Bookly-V', Lib\Plugin::getVersion() );
    }

    /**
     * @return string
     */
    public function getResolverMethodName()
    {
        return $this->resolver_method_name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $name
     * @param $default
     * @return mixed|null
     */
    protected function param( $name, $default = null )
    {
        return $this->params->has( $name )
            ? stripslashes_deep( $this->params->get( $name ) )
            : $default;
    }

    /**
     * @param string $key
     * @param string $format
     * @return string
     * @throws Api\Exceptions\ParameterException
     */
    protected function getDateFormattedParameter( $key, $format )
    {
        return $this->getDateTimeParameter( $key )->format( $format );
    }

    /**
     * @param string $key
     * @return \DateTime
     * @throws Api\Exceptions\ParameterException
     */
    protected function getDateTimeParameter( $key )
    {
        try {
            if ( $this->param( $key ) ) {
                $date_time = date_create( $this->param( $key ) );
                if ( $date_time ) {
                    return $date_time;
                }
            }
            throw new Api\Exceptions\ParameterException( $key, $this->param( $key ) );
        } catch ( \Error $e ) {
            throw new Api\Exceptions\ParameterException( $key, $this->param( $key ) );
        }
    }
}