import { Controller } from '@hotwired/stimulus';

import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import List from '@ckeditor/ckeditor5-list/src/list';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TableProperties from '@ckeditor/ckeditor5-table/src/tableproperties';
import TableCellProperties from '@ckeditor/ckeditor5-table/src/tablecellproperties';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor';
import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline';
import Link from '@ckeditor/ckeditor5-link/src/link';
import { FontSize } from '@ckeditor/ckeditor5-font';

/**
 * Gestion des ckeditor
 * utiliser le data-controller="editor"
 *
 * Ajouter le data-enable-variables="true" pour gérer l'insertion des variables
 */
export default class extends Controller {
    connect() {
        ClassicEditor
            .create(this.element, {
                language: 'fr',
                plugins: [
                    Essentials, Autoformat, Bold, Italic, List, FontBackgroundColor, FontSize,
                    Table, TableToolbar, TableProperties, TableCellProperties,
                    Heading, Link, Alignment, HorizontalLine
                ],
                toolbar: [
                    'bold', 'italic', 'fontBackgroundColor', '|', 'alignment', '|', 'numberedList', 'bulletedList',
                    '|', 'link', 'insertTable', '|', 'heading', '|', 'fontsize', '|',
                    !!this.element.dataset.enableVariables ? 'insertVariable' : '|'
                ],
                alignment: {
                    options: ['left', 'center', 'right']
                },
                fontSize: {
                    options: [
                        8,
                        9,
                        10,
                        11,
                        12,
                        13,
                        14,
                        15,
                        'default',
                        18,
                        20
                    ]
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
                }
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    editor.sourceElement.value = editor.getData();
                });
            })
        ;
    }
}
