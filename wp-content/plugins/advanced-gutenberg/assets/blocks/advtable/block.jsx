(function (wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents) {
    wpBlockEditor = wp.blockEditor || wp.editor;
    const {__} = wpI18n;
    const {Component, Fragment} = wpElement;
    const {registerBlockType, createBlock} = wpBlocks;
    const {InspectorControls, BlockControls, RichText, PanelColorSettings} = wpBlockEditor;
    const {PanelBody, BaseControl, RangeControl, SelectControl, ToggleControl, TextControl, Button, ToolbarGroup, ToolbarButton, DropdownMenu, Tooltip} = wpComponents;
    const {times} = lodash;

    const tableBlockIcon = (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="2 2 22 22">
            <path d="M3 3v18h18V3H3zm8 16H5v-6h6v6zm0-8H5V5h6v6zm8 8h-6v-6h6v6zm0-8h-6V5h6v6z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    );

    let willSetContent = null;
    let lastValue = '';

    const previewImageData = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAADxCAYAAADiK6r+AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAB5NJREFUeNrs3M+LE/0dwPGZyUTR1gV/0PUiDx68tTyFHnpQilCfHnqueuhBEfwD2mOhh6L24qXQS499CsVLF6TtvZdipQ9alIeqbPEkRSi1uhalbjLTiU/izo7fxE0yyebJvF4wT9x9ds3E+M5nJjtfowgAAAAAAAAAAAAAABZLPMvfPE3Tud0XfEnlvf90Op0vX+hF4PEY9+MFgMYEvYPP57OIPq458OrvGQ+5FTli3/o4D/z/WoOPZxh4POTXIkfsW78Oxf7ua4rY84UIvRJ5Ne7QZrJjkm+PethW23RPa468uiVjBg9NCD4UdRb4ePC18Yhz/NlP9NKbbqG4e7fJjRs3Vk+fPv29AwcOfDdJkqPl+83z3FNPY8Rx3Ps7/7rwtwcPHvz+0qVLf19fX++UIs9K23svBtNM9XiKyIdN8dYg8idPnvzgyJEjP261Wl83vWFLlmX/LoL/w/Xr13927dq156XAu5XYs/KRwKSxt2o6ZE/629vIT506tff+/ftXDh48+PNiiq+KHN6b7vv37NnzzZMnT35y4sSJP928efPlDgZwXLxAzG+iB6Z5UtrSR48efXL8+PFfFw/mgKcURtvY2Pjt4cOHf9Sf5tUtqxy+T3S+m9RwyF+OvXX06NH02LFjPxU57MzKysoP79y58/3oizfHW+XT32j7m9fVq01nHvqw8/NkbW3t43a7/bGnD3auGI5nApEnpUan+klVMuX+vXfoXhyyf8c5OYynOF//qBR6qzLNk2kinyj0wKHDtole+KqnDcacmHG8J3DInkQ1XXNSx6F7VN6pLMtMcxhT/5qS6iF79QKzuYc+7Gq4pNhhocP4ocdDAo/rCD6paT/f7Yir3WD6juqa5NOEHu9gugP1hP5eU4F/72Hm5+jDzjWAmtvazUN30xzqn+hRoKdd/Tm6iQ67M93nHrp/RAIWMO6ZTnRg8QgdhA4IHRA6IHRA6IDQAaEDQgehA0IHhA4IHRA6IHRA6IDQQeiA0AGhA0IHhA4IHRA6IHQQuj8CEDogdEDogNABoQNCB4QOCB2EDggdEDogdEDogNABoQNCB6EDQgeEDggdEDogdEDogNBB6MDSS3d7B549e9aIP+hDhw5Fr1+/frsts3379r3dmvK8jrKyshKlaboQ+2Kig0N3QOiA0AGhA0IHhA4IHRA6CB1YLrt+fV7vcsmmaLfbjXmMTXpeh07RJBF6E0PvXfe8KNc+e15N9LmyqGX5Areo5QsWtQBCB4QOCB0QOggdEDogdEDogNABoQNCB4bb9Svue4s9mmKw4KMJmvS8Cn0Hln01VznyTqcTbW5uLvXj7K1H763YasrzOsrevXsXZk260OcYei/yJjxeoW+96C1K6M7RoQGEDkIHhA4IHRA6IHRA6IDQAaGD0AGhA0IHFo316HNkPTqNDd169OViPfoW69EbGrr16M1iPTogdEDogNABoYPQAaEDQgeEDggdmBWLWubIohYaG7pFLcvFopYtFrU0NHSLWprFohZA6IDQAaEDQgehA0IHhA4IHRA6IHRgYlavzZHVa5jogNABoQNCB6EDQgeEDggdEDogdEDogNABoYPQAaEDQgeEDggdEDogdEDoIHRA6IDQAaEDQgeEDggdEDoIHRA6IHRA6IDQAaEDQgeEDggdhA4IHRA6IHRA6IDQAaEDQgehA0IHhA4IHRA6IHRA6IDQQej1yv2xwmI1lNS9U3Ece5pgwaJPat6h/M2bNxueGxhPlmX/GzT0gcjzeYde3ql3d37v3r2/OnyH8bx69eqfQ9oa9bmZhp6PCv/ixYufFzv9F08d7NytW7f+OGKi59NO9brO0d9tGxsb3bt37/6qOBT5r6cPPuzp06e/O3/+/GeljrJKV9t0Op25HbrnocgH24ULF/78+PHjXziEh9Fevnz52ZUrV34Z6CgLfG5iY79Fnqbp4Pt6W6v/YtG7Tftbu7+la2tr3z5z5sxP9u/f/41J7guWVTGV/7O+vv7puXPnfvPw4cNXxae6xbbZ3zql204/+u4g/uJ783mHnpRCbw0CL92mly9f/trZs2e/tbq6+lG73f5K7/vyPI+ETxMVp7Sbz58//9ft27c/v3r16j9evHix2Y+4U9oGwXcroedzC70fe1yJvRWIPS1N+lbpRSEu3a/YaZLqKe8g4G4p6s3SdO+WJvlgiyYJPZ1yp+PSDg+i71ZijioPTugIfXu83UrU3Sj8/lc06bl6WkPkUeXVqRxweSerE13sNC3wahNZJe5O5eMsquGNuGknejX48mTvBh6Y0BH79lYGIXcDUz040Sf50drEoffurDhPr071LPDAsn7cWSDyeNr3CmAJJnoWOIyvHr5H0xy21zHRQw8iCzygvHQOH5VuBU5TJ3posmeVj2v5GfrUoZV+1BYFpnVSuY0DgQsdsW+/QGZY5Pmkh+21hPaB2IdtIkfsO9umjry22AKxR0PCdl6O0MPn61EU/jHa1JHXHlv/QppoyOF5aJKLnSYGHoWCrn7dJBfGzCX0wHQfdj8CR/QjPq5jis8tuCHRR6JH3NvVOb0XJrL+CwA0Ut3TGgAAAAAAAAAAqPi/AAMAGqyWU8hzlH8AAAAASUVORK5CYII=';

    class AdvTable extends Component {
        constructor() {
            super(...arguments);
            this.state = {
                initRow: 3,
                initCol: 3,
                selectedCell: null,
                rangeSelected: null,
                multiSelected: null,
                sectionSelected: null,
                updated: false,
            };

            this.calculateRealColIndex = this.calculateRealColIndex.bind(this);
            this.isMultiSelected = this.isMultiSelected.bind(this);
            this.isRangeSelected = this.isRangeSelected.bind(this);
        }

        componentWillMount() {
            const {attributes, setAttributes} = this.props;
            const currentBlockConfig = advgbDefaultConfig['advgb-table'];

            // No override attributes of blocks inserted before
            if (attributes.changed !== true) {
                if (typeof currentBlockConfig === 'object' && currentBlockConfig !== null) {
                    Object.keys(currentBlockConfig).map((attribute) => {
                        if (typeof attributes[attribute] === 'boolean') {
                            attributes[attribute] = !!currentBlockConfig[attribute];
                        } else {
                            attributes[attribute] = currentBlockConfig[attribute];
                        }
                    });
                }

                // Finally set changed attribute to true, so we don't modify anything again
                setAttributes({changed: true});
            }
        }

        componentDidMount() {
            this.calculateRealColIndex('head');
        }

        componentDidUpdate() {
            const {isSelected} = this.props;
            const {selectedCell, updated} = this.state;

            if (!isSelected && selectedCell) {
                this.setState({
                    selectedCell: null,
                    rangeSelected: null,
                    multiSelected: null,
                });
            }

            if (updated) {
                this.calculateRealColIndex();
                this.setState({updated: false});
            }
        }

        createTable() {
            const {setAttributes} = this.props;
            const {initRow, initCol} = this.state;

            this.setState({updated: true});
            return setAttributes({
                body: times(parseInt(initRow), () => ({
                    cells: times(parseInt(initCol), () => ({
                        content: '',
                    }))
                }))
            });
        }

        // Check if is multi cells selected
        isMultiSelected() {
            const {multiSelected} = this.state;
            return (multiSelected && multiSelected.length > 1);
        }

        // Check if is range cells selected
        isRangeSelected() {
            const {rangeSelected} = this.state;
            return (rangeSelected && rangeSelected.toCell);
        }

        calculateRealColIndex() {
            const {attributes, setAttributes} = this.props;

            ['head', 'body', 'foot'].forEach((section) => {
                if (!attributes[section].length) return null;

                const newSection = attributes[section].map((row, cRow) => {
                    return {
                        cells: row.cells.map((cell, cCol) => {
                            cell.cI = cCol;
                            for (let i = 0; i < cRow; i++) {
                                for (let j = 0; j < attributes[section][i].cells.length; j++) {
                                    if (attributes[section][i].cells[j] && attributes[section][i].cells[j].colSpan) {
                                        if (attributes[section][i].cells[j].rowSpan && i + parseInt(attributes[section][i].cells[j].rowSpan) > cRow) {
                                            if (cCol === 0) {
                                                if (attributes[section][i].cells[j].cI <= cell.cI) {
                                                    cell.cI += parseInt(attributes[section][i].cells[j].colSpan);
                                                }
                                            } else {
                                                const lastColSpan = !isNaN(parseInt(row.cells[cCol - 1].colSpan)) ? parseInt(row.cells[cCol - 1].colSpan) : 0;
                                                if (attributes[section][i].cells[j].cI === row.cells[cCol - 1].cI + 1
                                                    || attributes[section][i].cells[j].cI <= row.cells[cCol - 1].cI + lastColSpan
                                                ) {
                                                    cell.cI += parseInt(attributes[section][i].cells[j].colSpan);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            for (let j = 0; j < cCol; j++) {
                                if (row.cells[j]) {
                                    if (row.cells[j].colSpan) {
                                        cell.cI += parseInt(row.cells[j].colSpan) - 1;
                                    }
                                }
                            }

                            return cell;
                        })
                    }
                });

                setAttributes({[section]: newSection});
            })
        }

        insertRow(offset) {
            const {selectedCell, sectionSelected} = this.state;

            if (!selectedCell) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const currentSection = attributes[sectionSelected];
            const {rowIndex} = selectedCell;
            const newRow = jQuery.extend(true, {}, currentSection[rowIndex]);
            newRow.cells.map((cell) => {
                cell.content = '';

                return cell;
            });
            newRow.cells = newRow.cells.filter((cCell) => !cCell.rowSpan);

            const newSection = [
                ...currentSection.slice(0, rowIndex + offset),
                newRow,
                ...currentSection.slice(rowIndex + offset),
            ].map((row, rowIdx) => ({
                cells: row.cells.map((cell) => {
                    if (cell.rowSpan) {
                        if (rowIdx <= rowIndex && ((rowIdx + parseInt(cell.rowSpan) - 1) >= rowIndex)) {
                            cell.rowSpan = parseInt(cell.rowSpan) + 1;
                        }
                    }
                    return cell;
                })
            }));

            this.setState({
                selectedCell: null,
                sectionSelected: null,
                updated: true,
            });
            setAttributes({[sectionSelected]: newSection});
        }

        deleteRow() {
            const {selectedCell, sectionSelected} = this.state;

            if (!selectedCell) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const currentSection = attributes[sectionSelected];
            const {rowIndex} = selectedCell;

            const newSection = currentSection.map((row, cRowIdx) => ({
                cells: row.cells.map((cell) => {
                    if (cell.rowSpan) {
                        if (cRowIdx <= rowIndex && parseInt(cell.rowSpan) + cRowIdx > rowIndex) {
                            cell.rowSpan = parseInt(cell.rowSpan) - 1;
                            if (cRowIdx === rowIndex) {
                                const findColIdx = currentSection[cRowIdx + 1].cells.findIndex((elm) => elm.cI === cell.cI || elm.cI > cell.cI);
                                currentSection[cRowIdx + 1].cells.splice(findColIdx, 0, cell);
                            }
                        }
                    }

                    return cell;
                })
            }));

            this.setState({
                selectedCell: null,
                sectionSelected: null,
                updated: true,
            });

            if (newSection.length < 2) {
                alert(__('At least 1 row of current section must present.', 'advanced-gutenberg'));
                return false;
            }

            setAttributes({
                [sectionSelected]: newSection.filter((row, index) => index !== rowIndex),
            });
        }

        insertColumn(offset) {
            const {selectedCell} = this.state;

            if (!selectedCell) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {cI} = selectedCell;
            let countRowSpan = 0;

            this.setState({selectedCell: null, updated: true});
            ['head', 'body', 'foot'].forEach((section) => (
                setAttributes({
                    [section]: attributes[section].map((row) => {
                        if (countRowSpan > 0) { // Skip if previous cell has row span
                            countRowSpan--;
                            return row;
                        }

                        let findColIdx = row.cells.findIndex((cell, idx) => cell.cI === cI || (row.cells[idx + 1] && row.cells[idx + 1].cI > cI));
                        if (findColIdx === -1) {
                            findColIdx = row.cells.length - 1;
                        }

                        if (row.cells[findColIdx].colSpan
                            && row.cells[findColIdx].cI < cI + offset
                            && row.cells[findColIdx].cI + parseInt(row.cells[findColIdx].colSpan) > cI + offset
                        ) {
                            row.cells[findColIdx].colSpan++;

                            if (row.cells[findColIdx].rowSpan) {
                                countRowSpan = parseInt(row.cells[findColIdx].rowSpan) - 1;
                            }

                            return row;
                        } else {
                            let realOffset = offset;
                            if (row.cells[findColIdx].cI > cI && offset === 1) {
                                realOffset = 0;
                            } else if (row.cells[findColIdx].cI < cI && offset === 0) {
                                realOffset = 1;
                            }

                            return {
                                cells: [
                                    ...row.cells.slice(0, findColIdx + realOffset),
                                    {content: ''},
                                    ...row.cells.slice(findColIdx + realOffset),
                                ],
                            }
                        }
                    }),
                })
            ))
        }

        deleteColumn() {
            const {selectedCell} = this.state;

            if (!selectedCell) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {cI} = selectedCell;
            let countRowSpan = 0;

            this.setState({selectedCell: null, updated: true});
            ['head', 'body', 'foot'].forEach((section) => (
                setAttributes({
                    [section]: attributes[section].map((row) => {
                        if (countRowSpan > 0) {
                            countRowSpan--;
                            return row;
                        }

                        const findColIdx = row.cells.findIndex((cell, idx) => cell.cI === cI || (row.cells[idx + 1] && row.cells[idx + 1].cI > cI));

                        if (row.cells[findColIdx].rowSpan) {
                            countRowSpan = parseInt(row.cells[findColIdx].rowSpan) - 1;
                        }

                        if (row.cells[findColIdx].colSpan) {
                            row.cells[findColIdx].colSpan--;
                            if (row.cells[findColIdx].colSpan <= 1) {
                                delete row.cells[findColIdx].colSpan;
                            }

                            return row;
                        }

                        return {
                            cells: row.cells.filter((cell, index) => index !== findColIdx),
                        }
                    }),
                })
            ))
        }

        mergeCells() {
            const {rangeSelected, sectionSelected} = this.state;

            if (!this.isRangeSelected()) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {fromCell, toCell} = rangeSelected;
            const currentSection = attributes[sectionSelected];
            const fCell = currentSection[fromCell.rowIdx].cells[fromCell.colIdx];
            const tCell = currentSection[toCell.rowIdx].cells[toCell.colIdx];
            const fcSpan = typeof fCell.colSpan === 'undefined' ? 0 : parseInt(fCell.colSpan) - 1;
            const frSpan = typeof fCell.rowSpan === 'undefined' ? 0 : parseInt(fCell.rowSpan) - 1;
            const tcSpan = typeof tCell.colSpan === 'undefined' ? 0 : parseInt(tCell.colSpan) - 1;
            const trSpan = typeof tCell.rowSpan === 'undefined' ? 0 : parseInt(tCell.rowSpan) - 1;
            const minRowIdx = Math.min(fromCell.rowIdx, toCell.rowIdx);
            const maxRowIdx = Math.max(fromCell.rowIdx + frSpan, toCell.rowIdx + trSpan);
            const minColIdx = Math.min(fromCell.RCI, toCell.RCI);
            const maxColIdx = Math.max(fromCell.RCI + fcSpan, toCell.RCI + tcSpan);

            const newSection = currentSection.map((row, curRowIndex) => {
                if (curRowIndex < minRowIdx || curRowIndex > maxRowIdx) {
                    return row;
                }

                return {
                    cells: row.cells.map((cell, curColIndex) => {
                        if (curColIndex === Math.min(fromCell.colIdx, toCell.colIdx)
                            && curRowIndex === Math.min(fromCell.rowIdx, toCell.rowIdx)
                        ) {
                            const rowSpan = Math.abs(maxRowIdx - minRowIdx) + 1;
                            const colSpan = Math.abs(maxColIdx - minColIdx) + 1;

                            return {
                                ...cell,
                                rowSpan: rowSpan > 1 ? rowSpan : undefined,
                                colSpan: colSpan > 1 ? colSpan : undefined,
                            }
                        }

                        return cell;
                    }).filter((cell, cCol) =>
                        cell.cI < minColIdx
                        || (cCol === Math.min(fromCell.colIdx, toCell.colIdx) && curRowIndex === Math.min(fromCell.rowIdx, toCell.rowIdx))
                        || cell.cI > maxColIdx
                    )
                }
            });

            setAttributes({[sectionSelected]: newSection});
            this.setState({
                selectedCell: null,
                sectionSelected: null,
                rangeSelected: null,
                updated: true,
            });
        }

        splitMergedCells() {
            const {selectedCell, sectionSelected} = this.state;

            if (!selectedCell) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {colIndex, rowIndex, cI} = selectedCell;

            const cellColSpan = attributes[sectionSelected][rowIndex].cells[colIndex].colSpan ? parseInt(attributes[sectionSelected][rowIndex].cells[colIndex].colSpan) : 1;
            const cellRowSpan = attributes[sectionSelected][rowIndex].cells[colIndex].rowSpan ? parseInt(attributes[sectionSelected][rowIndex].cells[colIndex].rowSpan) : 1;
            attributes[sectionSelected][rowIndex].cells[colIndex].colSpan = undefined;
            attributes[sectionSelected][rowIndex].cells[colIndex].rowSpan = undefined;

            const newSection = attributes[sectionSelected].map((row, curRowIndex) => {
                if (curRowIndex >= rowIndex && curRowIndex < (rowIndex + cellRowSpan)) {
                    const findColIdx = row.cells.findIndex((cell) => cell.cI >= cI);
                    let startRowFix = 0;
                    if (curRowIndex === rowIndex) {
                        startRowFix = 1;
                    }

                    return {
                        cells: [
                            ...row.cells.slice(0, findColIdx + startRowFix),
                            ...times(cellColSpan - startRowFix, () => ({content: ''})),
                            ...row.cells.slice(findColIdx + startRowFix),
                        ],
                    }
                }

                return row;
            });

            setAttributes({[sectionSelected]: newSection});
            this.setState({
                selectedCell: null,
                sectionSelected: null,
                updated: true,
            });
        }

        // Parse styles from HTML form to React styles object
        static parseStyles(styles) {
            if (typeof styles !== 'string') {
                return styles;
            }

            return styles
                .split(';')
                .filter(style => style.split(':')[0] && style.split(':')[1])
                .map(style => [
                    style.split(':')[0].trim().replace(/-./g, c => c.substr(1).toUpperCase()),
                    style.split(':')[1].trim()
                ])
                .reduce((styleObj, style) => ({
                    ...styleObj,
                    [style[0]]: style[1],
                }), {});
        }

        getCellStyles(style) {
            const {selectedCell, sectionSelected} = this.state;
            const section = this.props.attributes[sectionSelected];

            if (!selectedCell) return undefined;

            const {rowIndex, colIndex} = selectedCell;

            if (style === 'borderColor') {
                return section[rowIndex].cells[colIndex].borderColorSaved;
            }
            const styles = AdvTable.parseStyles(section[rowIndex].cells[colIndex].styles);

            if (typeof styles === 'object') {
                let convertedStyles = styles[style];

                if (convertedStyles && typeof convertedStyles !== 'number' && convertedStyles.indexOf('px')) {
                    convertedStyles = styles[style].replace(/px/g, '');
                }

                return typeof convertedStyles === 'undefined' && style === 'borderStyle' ? 'solid' : convertedStyles;
            } else {
                if (typeof styles !== 'undefined') {
                    let convertedStyles = styles[style];
                }

                return typeof convertedStyles === 'undefined' && style === 'borderStyle' ? 'solid' : undefined;
            }
        }

        updateCellsStyles(style) {
            const {selectedCell, rangeSelected, multiSelected, sectionSelected} = this.state;
            if (!selectedCell && !this.isRangeSelected() && !this.isMultiSelected()) {
                return null;
            }

            const {attributes, setAttributes} = this.props;
            const {rowIndex, colIndex} = selectedCell;
            const section = attributes[sectionSelected];
            let minRowIdx, maxRowIdx, minColIdx, maxColIdx;

            if (this.isRangeSelected()) {
                const {fromCell, toCell} = rangeSelected;
                const fCell = section[fromCell.rowIdx].cells[fromCell.colIdx];
                const tCell = section[toCell.rowIdx].cells[toCell.colIdx];
                const fcSpan = typeof fCell.colSpan === 'undefined' ? 0 : parseInt(fCell.colSpan) - 1;
                const frSpan = typeof fCell.rowSpan === 'undefined' ? 0 : parseInt(fCell.rowSpan) - 1;
                const tcSpan = typeof tCell.colSpan === 'undefined' ? 0 : parseInt(tCell.colSpan) - 1;
                const trSpan = typeof tCell.rowSpan === 'undefined' ? 0 : parseInt(tCell.rowSpan) - 1;
                minRowIdx = Math.min(fromCell.rowIdx, toCell.rowIdx);
                maxRowIdx = Math.max(fromCell.rowIdx + frSpan, toCell.rowIdx + trSpan);
                minColIdx = Math.min(fromCell.RCI, toCell.RCI);
                maxColIdx = Math.max(fromCell.RCI + fcSpan, toCell.RCI + tcSpan);
            }

            const newSection = section.map((row, curRowIndex) => {
                if (!this.isRangeSelected() && !this.isMultiSelected() && curRowIndex !== rowIndex
                    || (this.isRangeSelected() && (curRowIndex < minRowIdx || curRowIndex > maxRowIdx))
                    || (this.isMultiSelected() && multiSelected.findIndex((c) => c.rowIndex === curRowIndex) === -1)
                ) {
                    return row;
                }

                return {
                    cells: row.cells.map((cell, curColIndex) => {
                        if (!this.isRangeSelected() && !this.isMultiSelected() && curColIndex === colIndex
                            || (this.isRangeSelected() && (cell.cI >= minColIdx && cell.cI <= maxColIdx))
                            || (this.isMultiSelected() && multiSelected.findIndex((c) => c.colIndex === curColIndex && c.rowIndex === curRowIndex) > -1)
                        ) {
                            cell.styles = AdvTable.parseStyles(cell.styles);

                            if (style.borderColor) {
                                // Set border color
                                if (cell.styles.borderTopColor) {
                                    cell.styles = {...cell.styles, borderTopColor: style.borderColor};
                                }
                                if (cell.styles.borderRightColor) {
                                    cell.styles = {...cell.styles, borderRightColor: style.borderColor};
                                }
                                if (cell.styles.borderBottomColor) {
                                    cell.styles = {...cell.styles, borderBottomColor: style.borderColor};
                                }
                                if (cell.styles.borderLeftColor) {
                                    cell.styles = {...cell.styles, borderLeftColor: style.borderColor};
                                }

                                cell.borderColorSaved = style.borderColor;
                            } else if (style.setBorder) {
                                // Set border
                                const cellBorderColor = cell.borderColorSaved || '#000';
                                const cellColSpan = !cell.colSpan ? 0 : parseInt(cell.colSpan) - 1;
                                const cellRowSpan = !cell.rowSpan ? 0 : parseInt(cell.rowSpan) - 1;
                                switch (style.setBorder) {
                                    case 'top':
                                        cell.styles = {...cell.styles, borderTopColor: cellBorderColor};
                                        break;
                                    case 'right':
                                        cell.styles = {...cell.styles, borderRightColor: cellBorderColor};
                                        break;
                                    case 'bottom':
                                        cell.styles = {...cell.styles, borderBottomColor: cellBorderColor};
                                        break;
                                    case 'left':
                                        cell.styles = {...cell.styles, borderLeftColor: cellBorderColor};
                                        break;
                                    case 'all':
                                        cell.styles = {
                                            ...cell.styles,
                                            borderTopColor: cellBorderColor,
                                            borderRightColor: cellBorderColor,
                                            borderBottomColor: cellBorderColor,
                                            borderLeftColor: cellBorderColor,
                                        };
                                        break;
                                    case 'none':
                                        cell.styles = {
                                            ...cell.styles,
                                            borderTopColor: undefined,
                                            borderRightColor: undefined,
                                            borderBottomColor: undefined,
                                            borderLeftColor: undefined,
                                        };
                                        break;
                                    case 'vert':
                                        if (cell.cI === minColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderRightColor: cellBorderColor,
                                            };
                                        } else if (cell.cI + cellColSpan === maxColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderLeftColor: cellBorderColor,
                                            };
                                        } else {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderRightColor: cellBorderColor,
                                                borderLeftColor: cellBorderColor,
                                            };
                                        }
                                        break;
                                    case 'horz':
                                        if (curRowIndex === minRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderBottomColor: cellBorderColor,
                                            };
                                        } else if (curRowIndex + cellRowSpan === maxRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderTopColor: cellBorderColor,
                                            };
                                        } else {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderTopColor: cellBorderColor,
                                                borderBottomColor: cellBorderColor,
                                            };
                                        }
                                        break;
                                    case 'inner':
                                        if (curRowIndex === minRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderBottomColor: cellBorderColor,
                                            };
                                        } else if (curRowIndex + cellRowSpan === maxRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderTopColor: cellBorderColor,
                                            };
                                        } else {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderTopColor: cellBorderColor,
                                                borderBottomColor: cellBorderColor,
                                            };
                                        }

                                        if (cell.cI === minColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderRightColor: cellBorderColor,
                                            };
                                        } else if (cell.cI + cellColSpan === maxColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderLeftColor: cellBorderColor,
                                            };
                                        } else {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderRightColor: cellBorderColor,
                                                borderLeftColor: cellBorderColor,
                                            };
                                        }
                                        break;
                                    case 'outer':
                                        if (curRowIndex === minRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderTopColor: cellBorderColor,
                                            };
                                        } else if (curRowIndex + cellRowSpan === maxRowIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderBottomColor: cellBorderColor,
                                            };
                                        }

                                        if (cell.cI === minColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderLeftColor: cellBorderColor,
                                            };
                                        } else if (cell.cI + cellColSpan === maxColIdx) {
                                            cell.styles = {
                                                ...cell.styles,
                                                borderRightColor: cellBorderColor,
                                            };
                                        }
                                        break;
                                    default: // Nothing
                                        break;
                                }
                            } else {
                                cell.styles = {...cell.styles, ...style};
                            }
                        }

                        return cell;
                    })
                }
            });

            setAttributes({[section]: newSection});
        }

        updateCellContent(content, cell = null) {
            const {selectedCell, sectionSelected} = this.state;
            if (!selectedCell && !cell) {
                return null;
            }

            let rowIndex, colIndex;
            if (cell) {
                rowIndex = cell.rowIndex;
                colIndex = cell.colIndex;
            } else {
                rowIndex = selectedCell.rowIndex;
                colIndex = selectedCell.colIndex;
            }

            const {attributes, setAttributes} = this.props;

            const newSection = attributes[sectionSelected].map((row, curRowIndex) => {
                if (curRowIndex !== rowIndex) {
                    return row;
                }

                return {
                    cells: row.cells.map((cell, curColIndex) => {
                        if (curColIndex !== colIndex) {
                            return cell;
                        }

                        return {
                            ...cell,
                            content,
                        }
                    })
                }
            });

            setAttributes({[sectionSelected]: newSection});
        }

        toggleSection(section) {
            const {attributes, setAttributes} = this.props;
            const {sectionSelected} = this.state;
            const {body} = attributes;
            const cellsToAdd = [{cells: body[0].cells.map((cell) => ({cI: cell.cI, colSpan: cell.colSpan}))}];

            if (sectionSelected === section) {
                this.setState({
                    selectedCell: null,
                    sectionSelected: null,
                })
            }

            if (!attributes[section].length) {
                return setAttributes({[section]: cellsToAdd});
            }

            return setAttributes({[section]: []});
        }

        renderSection(section) {
            const {attributes} = this.props;
            const {selectedCell, multiSelected, rangeSelected, sectionSelected} = this.state;

            return attributes[section].map(({cells}, rowIndex) => (
                <tr key={rowIndex}>
                    {cells.map(({content, styles, colSpan, rowSpan, cI}, colIndex) => {
                        const cell = {rowIndex, colIndex, cI, section};

                        let isSelected = selectedCell
                            && selectedCell.rowIndex === rowIndex
                            && selectedCell.colIndex === colIndex
                            && sectionSelected === section;

                        if (this.isRangeSelected()) {
                            const {fromCell, toCell} = rangeSelected;
                            if (attributes[sectionSelected][fromCell.rowIdx] && attributes[sectionSelected][toCell.rowIdx]) {
                                const fCell = attributes[sectionSelected][fromCell.rowIdx].cells[fromCell.colIdx];
                                const tCell = attributes[sectionSelected][toCell.rowIdx].cells[toCell.colIdx];
                                const fcSpan = typeof fCell.colSpan === 'undefined' ? 0 : parseInt(fCell.colSpan) - 1;
                                const frSpan = typeof fCell.rowSpan === 'undefined' ? 0 : parseInt(fCell.rowSpan) - 1;
                                const tcSpan = typeof tCell.colSpan === 'undefined' ? 0 : parseInt(tCell.colSpan) - 1;
                                const trSpan = typeof tCell.rowSpan === 'undefined' ? 0 : parseInt(tCell.rowSpan) - 1;

                                isSelected = rowIndex >= Math.min(fromCell.rowIdx, toCell.rowIdx)
                                    && rowIndex <= Math.max(fromCell.rowIdx + frSpan, toCell.rowIdx + trSpan)
                                    && cI >= Math.min(fromCell.RCI, toCell.RCI)
                                    && cI <= Math.max(fromCell.RCI + fcSpan, toCell.RCI + tcSpan)
                                    && section === sectionSelected;
                            }
                        }

                        if (this.isMultiSelected()) {
                            isSelected = multiSelected.findIndex((c) => c.rowIndex === rowIndex && c.colIndex === colIndex) > -1
                                && multiSelected[0].section === section;
                        }


                        const cellClassName = [
                            isSelected && 'cell-selected',
                        ].filter(Boolean).join(' ');

                        styles = AdvTable.parseStyles(styles);

                        return (
                            <td key={colIndex}
                                className={cellClassName}
                                style={styles}
                                colSpan={colSpan}
                                rowSpan={rowSpan}
                                onClick={(e) => {
                                    if (e.shiftKey) {
                                        if (!rangeSelected) return;
                                        if (!rangeSelected.fromCell) return;

                                        const {fromCell} = rangeSelected;
                                        if (section !== fromCell.section) {
                                            alert(__('Cannot select multi cells from difference section!', 'advanced-gutenberg'));
                                            return;
                                        }
                                        const toCell = {
                                            rowIdx: rowIndex,
                                            colIdx: colIndex,
                                            RCI: cI,
                                            section: section,
                                        };

                                        this.setState({
                                            rangeSelected: {fromCell, toCell},
                                            multiSelected: null,
                                        });
                                    } else if (e.ctrlKey || e.metaKey) {
                                        const multiCells = multiSelected ? multiSelected : [];
                                        const existCell = multiCells.findIndex((cel) => cel.rowIndex === rowIndex && cel.colIndex === colIndex);

                                        if (multiCells.length && section !== multiCells[0].section) {
                                            alert(__('Cannot select multi cells from difference section!', 'advanced-gutenberg'));
                                            return;
                                        }

                                        if (existCell === -1) {
                                            multiCells.push(cell);
                                        } else {
                                            multiCells.splice(existCell, 1);
                                        }

                                        this.setState({
                                            multiSelected: multiCells,
                                            rangeSelected: null,
                                        });
                                    } else {
                                        this.setState({
                                            rangeSelected: {
                                                fromCell: {
                                                    rowIdx: rowIndex,
                                                    colIdx: colIndex,
                                                    RCI: cI,
                                                    section: section,
                                                },
                                            },
                                            multiSelected: [cell],
                                        });
                                    }
                                }}
                            >
                                <RichText
                                    className="wp-block-table__cell-content"
                                    value={content}
                                    onChange={(value) => {
                                        if (willSetContent) clearTimeout(willSetContent);
                                        lastValue = value;
                                        willSetContent = setTimeout(() => this.updateCellContent(value, selectedCell), 1000);
                                    }}
                                    unstableOnFocus={() => {
                                        if (willSetContent) {
                                            this.updateCellContent(lastValue, selectedCell);
                                            clearTimeout(willSetContent);
                                            willSetContent = null;
                                        }
                                        this.setState({
                                            selectedCell: cell,
                                            sectionSelected: section,
                                        })
                                    }}
                                />
                            </td>
                        )
                    })}
                </tr>
            ));
        }

        render() {
            const {attributes, setAttributes, className} = this.props;
            const {head, body, foot, maxWidth, tableCollapsed, hasFixedLayout, isPreview} = attributes;
            const {initRow, initCol, selectedCell, rangeSelected, multiSelected} = this.state;
            const maxWidthVal = !!maxWidth ? maxWidth : undefined;
            const currentCell = selectedCell ? body[selectedCell.rowIndex].cells[selectedCell.colIndex] : null;

            // First time insert block, let user determine the table
            if (!body.length) {
                return (
                    isPreview ?
                        <img alt={__('Advanced Table', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                        :
                        <Fragment>
                            <div className="advgb-init-table">
                                <TextControl
                                    type="number"
                                    label={__('Column Count', 'advanced-gutenberg')}
                                    value={initCol}
                                    onChange={(value) => this.setState({initCol: value})}
                                    min="1"
                                />
                                <TextControl
                                    type="number"
                                    label={__('Row Count', 'advanced-gutenberg')}
                                    value={initRow}
                                    onChange={(value) => this.setState({initRow: value})}
                                    min="1"
                                />
                                <Button isPrimary
                                        onClick={() => this.createTable()}>{__('Create', 'advanced-gutenberg')}</Button>
                                <div style={{marginTop: 10}}>
                                    <small>{__('Hint: Hold CTRL key for multi cells selection. Hold SHIFT key for range cells selection.', 'advanced-gutenberg')}</small>
                                </div>
                            </div>
                        </Fragment>
                )
            }

            const TABLE_CONTROLS = [
                {
                    icon: 'table-row-before',
                    title: __('Add Row Before', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.insertRow(0),
                },
                {
                    icon: 'table-row-after',
                    title: __('Add Row After', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.insertRow(1),
                },
                {
                    icon: 'table-row-delete',
                    title: __('Delete Row', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.deleteRow(),
                },
                {
                    icon: 'table-col-before',
                    title: __('Add Column Before', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.insertColumn(0),
                },
                {
                    icon: 'table-col-after',
                    title: __('Add Column After', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.insertColumn(1),
                },
                {
                    icon: 'table-col-delete',
                    title: __('Delete Column', 'advanced-gutenberg'),
                    isDisabled: !selectedCell || this.isRangeSelected() || this.isMultiSelected(),
                    onClick: () => this.deleteColumn(),
                },
                {
                    icon: (
                        <svg width="20" height="20" viewBox="4 2 18 18" className="dashicon">
                            <path fill="none" d="M0,0h24v24H0V0z"/>
                            <path d="M4,5v13h17V5H4z M14,7v9h-3V7H14z M6,7h3v9H6V7z M19,16h-3V7h3V16z"/>
                        </svg>
                    ),
                    title: __('Split Merged Cells', 'advanced-gutenberg'),
                    isDisabled: !selectedCell
                        || (currentCell && !currentCell.rowSpan && !currentCell.colSpan)
                        || this.isRangeSelected()
                        || this.isMultiSelected(),
                    onClick: () => this.splitMergedCells(),
                },
                {
                    icon: (
                        <svg width="20" height="20" className="dashicon" viewBox="2 2 22 22">
                            <path fill="none" d="M0,0h24v24H0V0z"/>
                            <polygon points="21,18 2,18 2,20 21,20 21,18"/>
                            <path
                                d="M19,10v4H4v-4H19 M20,8H3C2.45,8,2,8.45,2,9v6c0,0.55,0.45,1,1,1h17c0.55,0,1-0.45,1-1V9C21,8.45,20.55,8,20,8L20,8z"/>
                            <polygon points="21,4 2,4 2,6 21,6 21,4"/>
                        </svg>
                    ),
                    title: __('Merge Cells', 'advanced-gutenberg'),
                    isDisabled: !this.isRangeSelected(),
                    onClick: () => this.mergeCells(),
                },
            ];

            let BORDER_SELECT = [
                {
                    title: __('Border Top', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 21h2v-2H7v2zm0-8h2v-2H7v2zm4 0h2v-2h-2v2zm0 8h2v-2h-2v2zm-8-4h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2v-2H3v2zm0-4h2V7H3v2zm8 8h2v-2h-2v2zm8-8h2V7h-2v2zm0 4h2v-2h-2v2zM3 3v2h18V3H3zm16 14h2v-2h-2v2zm-4 4h2v-2h-2v2zM11 9h2V7h-2v2zm8 12h2v-2h-2v2zm-4-8h2v-2h-2v2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'top'}),
                },
                {
                    title: __('Border Right', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 21h2v-2H7v2zM3 5h2V3H3v2zm4 0h2V3H7v2zm0 8h2v-2H7v2zm-4 8h2v-2H3v2zm8 0h2v-2h-2v2zm-8-8h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm8 8h2v-2h-2v2zm4-4h2v-2h-2v2zm4-10v18h2V3h-2zm-4 18h2v-2h-2v2zm0-16h2V3h-2v2zm-4 8h2v-2h-2v2zm0-8h2V3h-2v2zm0 4h2V7h-2v2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'right'}),
                },
                {
                    title: __('Border Bottom', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M9 11H7v2h2v-2zm4 4h-2v2h2v-2zM9 3H7v2h2V3zm4 8h-2v2h2v-2zM5 3H3v2h2V3zm8 4h-2v2h2V7zm4 4h-2v2h2v-2zm-4-8h-2v2h2V3zm4 0h-2v2h2V3zm2 10h2v-2h-2v2zm0 4h2v-2h-2v2zM5 7H3v2h2V7zm14-4v2h2V3h-2zm0 6h2V7h-2v2zM5 11H3v2h2v-2zM3 21h18v-2H3v2zm2-6H3v2h2v-2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'bottom'}),
                },
                {
                    title: __('Border Left', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M11 21h2v-2h-2v2zm0-4h2v-2h-2v2zm0-12h2V3h-2v2zm0 4h2V7h-2v2zm0 4h2v-2h-2v2zm-4 8h2v-2H7v2zM7 5h2V3H7v2zm0 8h2v-2H7v2zm-4 8h2V3H3v18zM19 9h2V7h-2v2zm-4 12h2v-2h-2v2zm4-4h2v-2h-2v2zm0-14v2h2V3h-2zm0 10h2v-2h-2v2zm0 8h2v-2h-2v2zm-4-8h2v-2h-2v2zm0-8h2V3h-2v2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'left'}),
                },
                {
                    title: __('Border All', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M3 3v18h18V3H3zm8 16H5v-6h6v6zm0-8H5V5h6v6zm8 8h-6v-6h6v6zm0-8h-6V5h6v6z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'all'}),
                },
                {
                    title: __('Border None', 'advanced-gutenberg'),
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path
                                d="M7 5h2V3H7v2zm0 8h2v-2H7v2zm0 8h2v-2H7v2zm4-4h2v-2h-2v2zm0 4h2v-2h-2v2zm-8 0h2v-2H3v2zm0-4h2v-2H3v2zm0-4h2v-2H3v2zm0-4h2V7H3v2zm0-4h2V3H3v2zm8 8h2v-2h-2v2zm8 4h2v-2h-2v2zm0-4h2v-2h-2v2zm0 8h2v-2h-2v2zm0-12h2V7h-2v2zm-8 0h2V7h-2v2zm8-6v2h2V3h-2zm-8 2h2V3h-2v2zm4 16h2v-2h-2v2zm0-8h2v-2h-2v2zm0-8h2V3h-2v2z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    onClick: () => this.updateCellsStyles({setBorder: 'none'}),
                },
            ];

            if (this.isRangeSelected()) {
                const EXTRA_BORDER_SELECT = [
                    {
                        title: __('Border Vertical', 'advanced-gutenberg'),
                        icon: (
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path
                                    d="M3 9h2V7H3v2zm0-4h2V3H3v2zm4 16h2v-2H7v2zm0-8h2v-2H7v2zm-4 0h2v-2H3v2zm0 8h2v-2H3v2zm0-4h2v-2H3v2zM7 5h2V3H7v2zm12 12h2v-2h-2v2zm-8 4h2V3h-2v18zm8 0h2v-2h-2v2zm0-8h2v-2h-2v2zm0-10v2h2V3h-2zm0 6h2V7h-2v2zm-4-4h2V3h-2v2zm0 16h2v-2h-2v2zm0-8h2v-2h-2v2z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                        ),
                        onClick: () => this.updateCellsStyles({setBorder: 'vert'}),
                    },
                    {
                        title: __('Border Horizontal', 'advanced-gutenberg'),
                        icon: (
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path
                                    d="M3 21h2v-2H3v2zM5 7H3v2h2V7zM3 17h2v-2H3v2zm4 4h2v-2H7v2zM5 3H3v2h2V3zm4 0H7v2h2V3zm8 0h-2v2h2V3zm-4 4h-2v2h2V7zm0-4h-2v2h2V3zm6 14h2v-2h-2v2zm-8 4h2v-2h-2v2zm-8-8h18v-2H3v2zM19 3v2h2V3h-2zm0 6h2V7h-2v2zm-8 8h2v-2h-2v2zm4 4h2v-2h-2v2zm4 0h2v-2h-2v2z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                        ),
                        onClick: () => this.updateCellsStyles({setBorder: 'horz'}),
                    },
                    {
                        title: __('Border Inner', 'advanced-gutenberg'),
                        icon: (
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path
                                    d="M3 21h2v-2H3v2zm4 0h2v-2H7v2zM5 7H3v2h2V7zM3 17h2v-2H3v2zM9 3H7v2h2V3zM5 3H3v2h2V3zm12 0h-2v2h2V3zm2 6h2V7h-2v2zm0-6v2h2V3h-2zm-4 18h2v-2h-2v2zM13 3h-2v8H3v2h8v8h2v-8h8v-2h-8V3zm6 18h2v-2h-2v2zm0-4h2v-2h-2v2z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                        ),
                        onClick: () => this.updateCellsStyles({setBorder: 'inner'}),
                    },
                    {
                        title: __('Border Outer', 'advanced-gutenberg'),
                        icon: (
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path
                                    d="M13 7h-2v2h2V7zm0 4h-2v2h2v-2zm4 0h-2v2h2v-2zM3 3v18h18V3H3zm16 16H5V5h14v14zm-6-4h-2v2h2v-2zm-4-4H7v2h2v-2z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                        ),
                        onClick: () => this.updateCellsStyles({setBorder: 'outer'}),
                    },
                ];

                BORDER_SELECT = [...BORDER_SELECT, ...EXTRA_BORDER_SELECT];
            }

            const HORZ_ALIGNMENT_CONTROLS = [
                {
                    icon: 'editor-alignleft',
                    title: __('Align left', 'advanced-gutenberg'),
                    align: 'left',
                },
                {
                    icon: 'editor-aligncenter',
                    title: __('Align center', 'advanced-gutenberg'),
                    align: 'center',
                },
                {
                    icon: 'editor-alignright',
                    title: __('Align right', 'advanced-gutenberg'),
                    align: 'right',
                },
                {
                    icon: 'editor-justify',
                    title: __('Align justify', 'advanced-gutenberg'),
                    align: 'justify',
                },
            ];

            const VERT_ALIGNMENT_CONTROLS = [
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M8 11h3v10h2V11h3l-4-4-4 4zM4 3v2h16V3H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __('Align top', 'advanced-gutenberg'),
                    align: 'top',
                },
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M8 19h3v4h2v-4h3l-4-4-4 4zm8-14h-3V1h-2v4H8l4 4 4-4zM4 11v2h16v-2H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __('Align middle', 'advanced-gutenberg'),
                    align: 'middle',
                },
                {
                    icon: (
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M16 13h-3V3h-2v10H8l4 4 4-4zM4 19v2h16v-2H4z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                        </svg>
                    ),
                    title: __('Align bottom', 'advanced-gutenberg'),
                    align: 'bottom',
                },
            ];

            return (
                isPreview ?
                    <img alt={__('Advanced Table', 'advanced-gutenberg')} width='100%' src={previewImageData}/>
                    :
                    <Fragment>
                        <BlockControls>
                            <ToolbarGroup>
                                <DropdownMenu
                                    hasArrowIndicator
                                    icon="editor-table"
                                    label={__('Edit Table', 'advanced-gutenberg')}
                                    controls={TABLE_CONTROLS}
                                />
                                <ToolbarButton
                                    icon="update"
                                    label={__('Refresh table (Use this after using undo or redo)', 'advanced-gutenberg')}
                                    onClick={() => this.calculateRealColIndex()}
                                />
                            </ToolbarGroup>
                        </BlockControls>
                        <InspectorControls>
                            <PanelBody title={__('Table Settings', 'advanced-gutenberg')}>
                                <RangeControl
                                    label={__('Max width (px)', 'advanced-gutenberg')}
                                    help={__('Set this to 0 to make max-width is 100%', 'advanced-gutenberg')}
                                    min={0}
                                    max={1999}
                                    value={maxWidth}
                                    onChange={(value) => setAttributes({maxWidth: value})}
                                />
                                <ToggleControl
                                    label={__('Fixed width table cells', 'advanced-gutenberg')}
                                    checked={hasFixedLayout}
                                    onChange={() => setAttributes({hasFixedLayout: !hasFixedLayout})}
                                />
                                <ToggleControl
                                    label={__('Table header', 'advanced-gutenberg')}
                                    checked={head && head.length}
                                    onChange={() => this.toggleSection('head')}
                                />
                                <ToggleControl
                                    label={__('Table footer', 'advanced-gutenberg')}
                                    checked={foot && foot.length}
                                    onChange={() => this.toggleSection('foot')}
                                />
                                <ToggleControl
                                    label={__('Border collapsed', 'advanced-gutenberg')}
                                    checked={tableCollapsed}
                                    onChange={() => setAttributes({tableCollapsed: !tableCollapsed})}
                                />
                            </PanelBody>
                            <PanelBody title={__('Cell Settings', 'advanced-gutenberg')}>
                                <PanelColorSettings
                                    title={__('Color Settings', 'advanced-gutenberg')}
                                    colorSettings={[
                                        {
                                            label: __('Background Color', 'advanced-gutenberg'),
                                            value: this.getCellStyles('backgroundColor'),
                                            onChange: (value) => this.updateCellsStyles({backgroundColor: value}),
                                        },
                                        {
                                            label: __('Text Color', 'advanced-gutenberg'),
                                            value: this.getCellStyles('color'),
                                            onChange: (value) => this.updateCellsStyles({color: value}),
                                        },
                                    ]}
                                />
                                <PanelBody title={__('Border', 'advanced-gutenberg')} initialOpen={false}>
                                    <div className="advgb-border-item-wrapper">
                                        {BORDER_SELECT.map((item, index) => (
                                            <div className="advgb-border-item" key={index}>
                                                <Tooltip text={item.title}>
                                                    <span onClick={item.onClick}>{item.icon}</span>
                                                </Tooltip>
                                            </div>
                                        ))}
                                    </div>
                                    <SelectControl
                                        label={__('Border Style', 'advanced-gutenberg')}
                                        value={this.getCellStyles('borderStyle')}
                                        options={[
                                            {label: __('Solid', 'advanced-gutenberg'), value: 'solid'},
                                            {label: __('Dashed', 'advanced-gutenberg'), value: 'dashed'},
                                            {label: __('Dotted', 'advanced-gutenberg'), value: 'dotted'},
                                            {label: __('None', 'advanced-gutenberg'), value: 'none'},
                                        ]}
                                        onChange={(value) => this.updateCellsStyles({borderStyle: value})}
                                    />
                                    <RangeControl
                                        label={__('Border width', 'advanced-gutenberg')}
                                        value={this.getCellStyles('borderWidth') || 0}
                                        min={0}
                                        max={10}
                                        onChange={(value) => this.updateCellsStyles({borderWidth: value})}
                                    />
                                    <PanelColorSettings
                                        title={__('Border Color', 'advanced-gutenberg')}
                                        colorSettings={[
                                            {
                                                label: __('Border Color', 'advanced-gutenberg'),
                                                value: this.getCellStyles('borderColor'),
                                                onChange: (value) => this.updateCellsStyles({borderColor: value}),
                                            },
                                        ]}
                                    />
                                </PanelBody>
                                <PanelBody title={__('Padding', 'advanced-gutenberg')} initialOpen={false}>
                                    <RangeControl
                                        label={__('Padding Top', 'advanced-gutenberg')}
                                        value={this.getCellStyles('paddingTop') || 0}
                                        min={0}
                                        max={100}
                                        onChange={(value) => this.updateCellsStyles({paddingTop: value})}
                                    />
                                    <RangeControl
                                        label={__('Padding Right', 'advanced-gutenberg')}
                                        value={this.getCellStyles('paddingRight') || 0}
                                        min={0}
                                        max={100}
                                        onChange={(value) => this.updateCellsStyles({paddingRight: value})}
                                    />
                                    <RangeControl
                                        label={__('Padding Bottom', 'advanced-gutenberg')}
                                        value={this.getCellStyles('paddingBottom') || 0}
                                        min={0}
                                        max={100}
                                        onChange={(value) => this.updateCellsStyles({paddingBottom: value})}
                                    />
                                    <RangeControl
                                        label={__('Padding Left', 'advanced-gutenberg')}
                                        value={this.getCellStyles('paddingLeft') || 0}
                                        min={0}
                                        max={100}
                                        onChange={(value) => this.updateCellsStyles({paddingLeft: value})}
                                    />
                                </PanelBody>
                                <PanelBody title={__('Text Alignment', 'advanced-gutenberg')} initialOpen={false}>
                                    <BaseControl label={__('Horizontal Align', 'advanced-gutenberg')}>
                                        <ToolbarGroup
                                            controls={HORZ_ALIGNMENT_CONTROLS.map((control) => {
                                                const isActive = (this.getCellStyles('textAlign') === control.align);

                                                return {
                                                    ...control,
                                                    isActive,
                                                    onClick: () => this.updateCellsStyles({textAlign: isActive ? undefined : control.align}),
                                                };
                                            })}
                                        />
                                    </BaseControl>
                                    <BaseControl label={__('Vertical Align', 'advanced-gutenberg')}>
                                        <ToolbarGroup
                                            controls={VERT_ALIGNMENT_CONTROLS.map((control) => {
                                                const isActive = (this.getCellStyles('verticalAlign') === control.align);

                                                return {
                                                    ...control,
                                                    isActive,
                                                    onClick: () => this.updateCellsStyles({verticalAlign: isActive ? undefined : control.align}),
                                                };
                                            })}
                                        />
                                    </BaseControl>
                                </PanelBody>
                            </PanelBody>
                        </InspectorControls>
                        <table className={className}
                               style={{
                                   maxWidth: maxWidthVal,
                                   borderCollapse: tableCollapsed ? 'collapse' : undefined,
                                   tableLayout: hasFixedLayout ? 'fixed' : undefined,
                               }}
                        >
                            {!!head.length && (
                                <thead>{this.renderSection('head')}</thead>
                            )}
                            <tbody>{this.renderSection('body')}</tbody>
                            {!!foot.length && (
                                <tfoot>{this.renderSection('foot')}</tfoot>
                            )}
                        </table>
                    </Fragment>
            )
        }
    }

    registerBlockType('advgb/table', {
        title: __('Advanced Table', 'advanced-gutenberg'),
        description: __('Advanced table block with more styles and functions.', 'advanced-gutenberg'),
        icon: {
            src: tableBlockIcon,
            foreground: typeof advgbBlocks !== 'undefined' ? advgbBlocks.color : undefined,
        },
        category: 'advgb-category',
        keywords: [__('table', 'advanced-gutenberg'), __('cell', 'advanced-gutenberg'), __('data', 'advanced-gutenberg')],
        attributes: {
            head: {
                type: 'array',
                default: [],
                source: 'query',
                selector: 'thead tr',
                query: {
                    cells: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'td, th',
                        query: {
                            content: {
                                source: 'html',
                            },
                            styles: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'style',
                            },
                            colSpan: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'colspan',
                            },
                            borderColorSaved: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'data-border-color',
                            }
                        },
                    },
                },
            },
            body: {
                type: 'array',
                default: [],
                source: 'query',
                selector: 'tbody tr',
                query: {
                    cells: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'td',
                        query: {
                            content: {
                                source: 'html',
                            },
                            styles: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'style',
                            },
                            colSpan: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'colspan',
                            },
                            rowSpan: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'rowspan',
                            },
                            borderColorSaved: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'data-border-color',
                            }
                        },
                    },
                },
            },
            foot: {
                type: 'array',
                default: [],
                source: 'query',
                selector: 'tfoot tr',
                query: {
                    cells: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'td, th',
                        query: {
                            content: {
                                source: 'html',
                            },
                            styles: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'style',
                            },
                            colSpan: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'colspan',
                            },
                            borderColorSaved: {
                                type: 'string',
                                source: 'attribute',
                                attribute: 'data-border-color',
                            }
                        },
                    },
                },
            },
            maxWidth: {
                type: 'number',
                default: 0
            },
            hasFixedLayout: {
                type: 'boolean',
                default: false,
            },
            tableCollapsed: {
                type: 'boolean',
                default: false,
            },
            changed: {
                type: 'boolean',
                default: false,
            },
            isPreview: {
                type: 'boolean',
                default: false,
            },
        },
        example: {
            attributes: {
                isPreview: true
            },
        },
        supports: {
            align: true,
            anchor: true
        },
        styles: [
            {name: 'default', label: __('Default', 'advanced-gutenberg'), isDefault: true},
            {name: 'stripes', label: __('Stripes', 'advanced-gutenberg')},
        ],
        edit: AdvTable,
        save: function ({attributes}) {
            const {head, body, foot, maxWidth, tableCollapsed, hasFixedLayout} = attributes;
            const maxWidthVal = !!maxWidth ? maxWidth : undefined;

            function renderSection(section) {
                let sectionTagName = section === 'head' ? 'th' : 'td';
                return attributes[section].map(({cells}, rowIndex) => (
                    <tr key={rowIndex}>
                        {cells.map(({content, styles, colSpan, rowSpan, borderColorSaved}, colIndex) => (
                            <RichText.Content
                                tagName={sectionTagName}
                                value={content}
                                key={colIndex}
                                style={styles}
                                colSpan={colSpan}
                                rowSpan={rowSpan}
                                data-border-color={borderColorSaved}
                            />
                        ))}
                    </tr>
                ))
            }

            return (
                <table className="advgb-table-frontend"
                       style={{
                           maxWidth: maxWidthVal,
                           borderCollapse: tableCollapsed ? 'collapse' : undefined,
                           tableLayout: hasFixedLayout ? 'fixed' : undefined,
                       }}
                >
                    {!!head.length && (
                        <thead>{renderSection('head')}</thead>
                    )}
                    <tbody>{renderSection('body')}</tbody>
                    {!!foot.length && (
                        <tfoot>{renderSection('foot')}</tfoot>
                    )}
                </table>
            );
        },
        transforms: {
            from: [
                {
                    type: 'block',
                    blocks: ['core/table'],
                    transform: (attributes) => {
                        return createBlock('advgb/table', {
                            body: attributes.body,
                        })
                    }
                },
            ],
        },
        deprecated: [
            {
                attributes: {
                    head: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'thead tr',
                        query: {
                            cells: {
                                type: 'array',
                                default: [],
                                source: 'query',
                                selector: 'td, th',
                                query: {
                                    content: {
                                        source: 'html',
                                    },
                                    styles: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'style',
                                    },
                                    colSpan: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'colspan',
                                    },
                                    borderColorSaved: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'data-border-color',
                                    }
                                },
                            },
                        },
                    },
                    body: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'tbody tr',
                        query: {
                            cells: {
                                type: 'array',
                                default: [],
                                source: 'query',
                                selector: 'td',
                                query: {
                                    content: {
                                        source: 'html',
                                    },
                                    styles: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'style',
                                    },
                                    colSpan: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'colspan',
                                    },
                                    rowSpan: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'rowspan',
                                    },
                                    borderColorSaved: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'data-border-color',
                                    }
                                },
                            },
                        },
                    },
                    foot: {
                        type: 'array',
                        default: [],
                        source: 'query',
                        selector: 'tfoot tr',
                        query: {
                            cells: {
                                type: 'array',
                                default: [],
                                source: 'query',
                                selector: 'td, th',
                                query: {
                                    content: {
                                        source: 'html',
                                    },
                                    styles: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'style',
                                    },
                                    colSpan: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'colspan',
                                    },
                                    borderColorSaved: {
                                        type: 'string',
                                        source: 'attribute',
                                        attribute: 'data-border-color',
                                    }
                                },
                            },
                        },
                    },
                    maxWidth: {
                        type: 'number',
                        default: 0
                    },
                    hasFixedLayout: {
                        type: 'boolean',
                        default: false,
                    },
                    tableCollapsed: {
                        type: 'boolean',
                        default: false,
                    },
                    changed: {
                        type: 'boolean',
                        default: false,
                    },
                    isPreview: {
                        type: 'boolean',
                        default: false,
                    },
                },
                save: function ({attributes}) {
                    const {head, body, foot, maxWidth, tableCollapsed, hasFixedLayout} = attributes;
                    const maxWidthVal = !!maxWidth ? maxWidth : undefined;

                    function renderSection(section) {
                        return attributes[section].map(({cells}, rowIndex) => (
                            <tr key={rowIndex}>
                                {cells.map(({content, styles, colSpan, rowSpan, borderColorSaved}, colIndex) => (
                                    <RichText.Content
                                        tagName="td"
                                        value={content}
                                        key={colIndex}
                                        style={styles}
                                        colSpan={colSpan}
                                        rowSpan={rowSpan}
                                        data-border-color={borderColorSaved}
                                    />
                                ))}
                            </tr>
                        ))
                    }

                    return (
                        <table className="advgb-table-frontend"
                               style={{
                                   maxWidth: maxWidthVal,
                                   borderCollapse: tableCollapsed ? 'collapse' : undefined,
                                   tableLayout: hasFixedLayout ? 'fixed' : undefined,
                               }}
                        >
                            {!!head.length && (
                                <thead>{renderSection('head')}</thead>
                            )}
                            <tbody>{renderSection('body')}</tbody>
                            {!!foot.length && (
                                <tfoot>{renderSection('foot')}</tfoot>
                            )}
                        </table>
                    );
                },
            }
        ]
    });
})(wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components);
