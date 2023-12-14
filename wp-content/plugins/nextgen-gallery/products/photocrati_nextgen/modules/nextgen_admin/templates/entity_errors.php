<div class="entity_errors">
	<p>Please correct the following:</p>
	<ul>
		<?php foreach ( $entity->get_errors() as $property => $errors ) : ?>
			<?php foreach ( $errors as $error ) : ?>
				<li><?php echo esc_html( $error ); ?></li>
			<?php endforeach ?>
		<?php endforeach ?>
	</ul>
</div>