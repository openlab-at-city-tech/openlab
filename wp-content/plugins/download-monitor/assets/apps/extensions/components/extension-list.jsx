import styles from './extension-list.module.scss';
import ExtensionTable from './extension-table';

export default function ExtensionList() {
	return (
		<div className={ styles.extensionWrapper }>
			<ExtensionTable />
		</div>
	);
}
