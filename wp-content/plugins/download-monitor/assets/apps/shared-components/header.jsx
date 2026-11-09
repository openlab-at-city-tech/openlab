import styles from './header.module.scss';
import dlmLogo from '../../images/logo.png';

export default function Header() {
	return (
		<div className={ styles.pageHeader }>
			<div className={ styles.logoContainer }>
				<img src={ dlmLogo } alt="Download Monitor logo" className={ styles.logo } />
				<span className={ styles.logoText }>Download Monitor</span>
			</div>
		</div>
	);
}
