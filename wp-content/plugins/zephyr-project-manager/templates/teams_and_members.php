<?php
	/*
	* Teams & Members Page
	* This page is used to display and manage teams and team members as well as add and remove them
	*/
	
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Core\Tasks;
	use Inc\Core\Projects;
	use Inc\Base\BaseController;
	use Inc\Core\Utillities;
	use Inc\Core\Members;

	$Projects = new Projects;
	$all_members = Members::get_zephyr_members();
	$admin = current_user_can( 'administrator' );

	$limit = 10;
	$users = Members::get_members( $limit, 1 );

	$user_count = count_users();
	$total_users = $user_count['total_users'];
	$pages = ceil($total_users / $limit);
?>

<main class="zpm_settings_wrap">

	<?php $this->get_header(); ?>

	<div id="zpm_container">
		
		<div class="zpm_body zpm_body_no_background">
			<?php if (isset($_GET['action']) && $_GET['action'] == 'edit_member') : ?>
				<?php include('edit_members.php'); ?>
			<?php else: ?>
				<h1 class="zpm_page_title"><?php _e( 'Members', 'zephyr-project-manager' ); ?></h1>
				
				<div class="zpm-buttons-right zpm-buttons-title">
					<button id="zpm-members__bulk-access-btn" class="zpm_button"><?php _e( 'Bulk Edit Access', 'zephyr-project-manager' ); ?></button>
				</div>
				<div id="zpm-member-list__table" class="zpm-table">
					<?php foreach ( $users as $user ) : ?>
						<?php echo Members::list_html( $user ); ?>
					<?php endforeach; ?>
				</div>
				<div class="zpm-table-pagination">
					<?php for($i = 1; $i <= $pages; $i++) : ?>

						<button data-page="<?php echo $i; ?>" data-zpm-pages="<?php echo $pages; ?>" class="zpm_button zpm-button__next zpm-members-pagination__page" <?php echo $i == '1' ? 'disabled' : ''; ?>><?php echo $i; ?></button>
					<?php endfor; ?>

				</div>

				<h1 class="zpm_page_title"><?php _e( 'Teams', 'zephyr-project-manager' ); ?></h1>
				<?php if (Utillities::canCreateTeams()) : ?>
					<div id="zpm-new-team-btn__wrap" class="zpm-buttons-right">
						<button id="zpm-create-team-btn" class="zpm_button" data-zpm-modal-trigger="zpm-new-team-modal"><?php _e( 'New Team', 'zephyr-project-manager' ); ?></button>
					</div>
				<?php endif; ?>


				<?php
					$teams = Members::get_teams();
				?>
				<div class="zpm-teams-list" id="zpm_members">

					<?php foreach ($teams as $team) : ?>
						<?php echo Members::team_single_html( $team ) ?>
					<?php endforeach; ?>
					<?php if (sizeof( $teams ) <= 0) : ?>
						<p id="zpm-no-teams-notice" class="zpm-no-results-error"><?php _e( 'There are no teams yet...', 'zephyr-project-manager' ); ?></p>
					<?php endif; ?>
				</div>

			<?php endif; ?>
		</div>
	</div>
</main>

<div id="zpm-new-team-modal" class="zpm-modal">
	<?php if (Utillities::canCreateTeams()) : ?>
		<h3 class="zpm-modal-title"><?php _e( 'New Team', 'zephyr-project-manager' ); ?></h3>
	<?php endif; ?>

	<div class="zpm-form__group">
		<input type="text" name="zpm-new-team-name" id="zpm-new-team-name" class="zpm-form__field" placeholder="<?php _e( 'Team Name', 'zephyr-project-manager' ); ?>">
		<label for="zpm-new-team-name" class="zpm-form__label"><?php _e( 'Team Name', 'zephyr-project-manager' ); ?></label>
	</div>

	<div class="zpm-form__group">
		<textarea type="text" name="zpm-new-team-description" id="zpm-new-team-description" class="zpm-form__field" placeholder="<?php _e( 'Team Description', 'zephyr-project-manager' ); ?>"></textarea>
		<label for="zpm-new-team-description" class="zpm-form__label"><?php _e( 'Team Description', 'zephyr-project-manager' ); ?></label>
	</div>

	<ul class="zpm-new-team-member-list">
		<?php foreach ($all_members as $member) : ?>
			<?php if(!isset($member['id']) || !isset($member['name'])) { continue; } ?>
			<li>
				<span class="zpm-memeber-toggle">
					<input type="checkbox" id="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-new-team-member" data-member-id="<?php echo isset($member['id']) ? $member['id'] : '';; ?>" >
					<label for="<?php echo 'zpm-member-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
					</label>
				</span>
				<?php echo $member['name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="zpm-buttons-right">
		<button id="zpm-new-team" class="zpm_button"><?php _e( 'Create Team', 'zephyr-project-manager' ); ?></button>
	</div>
</div>

<div id="zpm-edit-team-modal" class="zpm-modal">

	<div id="zpm-modal-loader-edit-team" class="zpm-modal-preloader">
		<div class="zpm-loader-holder"><div class="zpm-loader"></div></div>
	</div>

	<h3 class="zpm-modal-title"><?php _e( 'Edit Team', 'zephyr-project-manager' ); ?></h3>

	<div class="zpm-form__group">
		<input type="text" name="zpm-edit-team-name" id="zpm-edit-team-name" class="zpm-form__field" placeholder="<?php _e( 'Team Name', 'zephyr-project-manager' ); ?>">
		<label for="zpm-edit-team-name" class="zpm-form__label"><?php _e( 'Team Name', 'zephyr-project-manager' ); ?></label>
	</div>

	<div class="zpm-form__group">
		<input type="text" name="zpm-edit-team-description" id="zpm-edit-team-description" class="zpm-form__field" placeholder="<?php _e( 'Team Description', 'zephyr-project-manager' ); ?>">
		<label for="zpm-edit-team-description" class="zpm-form__label"><?php _e( 'Team Description', 'zephyr-project-manager' ); ?></label>
	</div>

	<input type="hidden" id="zpm-edit-team-id" />

	<ul class="zpm-edit-team-member-list">
		<?php foreach ($all_members as $member) : ?>
			<li>
				<span class="zpm-memeber-toggle">
					<input type="checkbox" id="<?php echo 'zpm-member-edit-toggle-' . $member['id']; ?>" class="zpm-toggle zpm-edit-team-member" data-member-id="<?php echo $member['id']; ?>">
					<label for="<?php echo 'zpm-member-edit-toggle-' . $member['id']; ?>" class="zpm-toggle-label">
					</label>
				</span>
				<?php echo $member['name']; ?>
			</li>
		<?php endforeach; ?>
	</ul>

	<div class="zpm-buttons-right">
		<button id="zpm-edit-team" class="zpm_button"><?php _e( 'Save Changes', 'zephyr-project-manager' ); ?></button>
	</div>
</div>
<?php $this->get_footer(); ?>