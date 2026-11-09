import Navigation from './Navigation';
import Content from './Content';
import styles from './SettingsPage.module.scss';

export default function SettingsPage() {
	return (
		<div className={ styles.page }>
			<Navigation />
			<Content />
		</div>
	);
}
