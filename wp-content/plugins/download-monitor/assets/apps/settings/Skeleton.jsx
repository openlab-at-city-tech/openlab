import styles from './Skeleton.module.scss';

export function SkeletonPanel() {
	return (
		<div className={ styles.panel }>
			<div className={ `${ styles.bar } ${ styles.title }` } />
			{ [ 0, 1, 2 ].map( ( row ) => (
				<div key={ row } className={ styles.row }>
					<div className={ `${ styles.bar } ${ styles.label }` } />
					<div className={ `${ styles.bar } ${ styles.input }` } />
				</div>
			) ) }
		</div>
	);
}

export default function Skeleton( { count = 2 } ) {
	return (
		<div className={ styles.content }>
			{ Array.from( { length: count } ).map( ( _, index ) => (
				<SkeletonPanel key={ index } />
			) ) }
		</div>
	);
}
