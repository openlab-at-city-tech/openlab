import classnames from 'classnames';

import QuestionHeader from './QuestionHeader';
import QuestionBody from './QuestionBody';
import QuestionFeedback from './QuestionFeedback';

export default ( {
	classes,
	header,
	onChangeHeader,
	body,
	onChangeBody,
	singleFeedback,
	feedback,
	onChangeFeedback,
	textAlignment,
	textColorControl,
	backgroundColorControl,
	fontSize,
	children,
} ) => {
	const styles = {
		textAlignment,
		textColorControl,
		backgroundColorControl,
		fontSize,
	};
	return (
		<div className={ classnames( 'bulb-question', classes ) }>
			<QuestionHeader value={ header } onChange={ onChangeHeader } { ...styles } />
			<QuestionBody value={ body } onChange={ onChangeBody } { ...styles } />
			{ children }
			<QuestionFeedback
				singleFeedback={ singleFeedback }
				feedback={ feedback }
				onChangeFeedback={ onChangeFeedback }
				{ ...styles }
			/>
		</div>
	);
};
