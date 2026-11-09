import styles from './BlacklistStatusField.module.scss';

export default function BlacklistStatusField( { field } ) {
	return (
		<p className={ styles.status }>
			<span className={ `dashicons ${ field.updated ? 'dashicons-saved' : 'dashicons-no' } ${ field.updated ? styles.ok : styles.error }` } />
			<span>{ field.status_label }</span>
		</p>
	);
}
