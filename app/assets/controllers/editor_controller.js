import {Controller} from '@hotwired/stimulus';

import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Underline from '@ckeditor/ckeditor5-basic-styles/src/underline';
import List from '@ckeditor/ckeditor5-list/src/list';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TableProperties from '@ckeditor/ckeditor5-table/src/tableproperties';
import TableCellProperties from '@ckeditor/ckeditor5-table/src/tablecellproperties';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor';
import HorizontalLine from '@ckeditor/ckeditor5-horizontal-line/src/horizontalline';
import Link from '@ckeditor/ckeditor5-link/src/link';
import AutoLink from '@ckeditor/ckeditor5-link/src/autolink';
import {FontSize, FontColor} from '@ckeditor/ckeditor5-font';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import TextTransformation from "@ckeditor/ckeditor5-typing/src/texttransformation";
import Undo from "@ckeditor/ckeditor5-undo/src/undo";
import RemoveFormat from "@ckeditor/ckeditor5-remove-format/src/removeformat";

/**
 * Gestion des ckeditorlist-style-type
 * utiliser le data-controller="editor"
 *
 */
export default class extends Controller {
    connect() {
        ClassicEditor
            .create(this.element, {
                language: 'fr',
                plugins: [
                    Essentials, Autoformat, Bold, Italic, List, FontBackgroundColor, FontSize,
                    Table, TableToolbar, TableProperties, TableCellProperties,
                    Heading, Link, Alignment, HorizontalLine,
                    BlockQuote, this.DisallowNestingBlockQuotes, Undo, FontColor, RemoveFormat, AutoLink, Underline,
                    this.RemoveFormatLinks, TextTransformation, Underline
                ],
                toolbar: {
                    items: [
                        'undo', 'redo', '|',
                        'heading', 'bold', 'italic', 'underline', '|',
                        'fontColor', 'fontsize', '|',
                        'alignment', 'blockQuote', 'removeformat', '|',
                        'numberedList', 'bulletedList', '|',
                        'link', 'insertTable'
                    ],
                    shouldNotGroupWhenFull: true
                },
                alignment: {
                    options: ['left', 'center', 'right']
                },
                fontSize: {
                    options: [
                        9,
                        11,
                        13,
                        'default',
                        17,
                        19,
                        21
                    ]
                },
                link: {
                    decorators: {
                        addTargetToExternalLinks: {
                            mode: 'automatic',
                            callback: url => /^(https?:)?\/\//.test( url ),
                            attributes: {
                                target: '_blank',
                                rel: 'noopener noreferrer'
                            }
                        }
                    }
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
                },
                typing: {
                    transformations: {
                        // remove: [
                        //     // Do not use the transformations from the
                        //     // 'symbols' and 'quotes' groups.
                        //     'symbols',
                        //     'quotes',
                        //
                        //     // As well as the following transformations.
                        //     'arrowLeft',
                        //     'arrowRight'
                        // ],
                        extra: [
                            // Add some custom transformations – e.g. for emojis.
                            {from: ':)', to: '🙂'},
                            {from: ':D', to: '😄'},
                            {from: '<3', to: '❤️'},
                            {from: ';)', to: '😉'},
                            {from: ':P', to: '😛'},
                            {from: ':*', to: '😘'},
                            {from: ':(', to: '☹️'},
                            {from: ':O', to: '😲'},
                            {from: ':|', to: '😐'},
                            {from: ':]', to: '😊'},
                            {from: ':$', to: '🤑'},
                            {from: ':]', to: '😇'},
                            {from: ':$', to: '🤑'},
                            {from: 'XD', to: '😆'},
                            {from: ':B', to: '😎'},
                            {from: ':*', to: '😗'},
                            {from: ':\'(', to: '😢'},
                            {from: ':\'D', to: '😂'},
                            {from: ':fire:', to: '🔥'},
                            {from: ':thumbsup:', to: '👍'},
                            {from: ':thumbsdown:', to: '👎'},
                            {from: ':muscle:', to: '💪'},
                            {from: ':victory:', to: '✌️'},
                            {from: ':pray:', to: '🙏'},
                            {from: ':heart:', to: '❤️'},
                            {from: ':star:', to: '⭐'},
                            {from: ':rainbow:', to: '🌈'},
                            {from: ':coffee:', to: '☕'},
                            {from: ':pizza:', to: '🍕'},
                            {from: ':taco:', to: '🌮'},
                            {from: ':burrito:', to: '🌯'},
                            {from: ':cake:', to: '🍰'},
                            {from: ':wine:', to: '🍷'},
                            {from: ':beer:', to: '🍺'},
                            {from: ':cocktail:', to: '🍹'},
                            {from: ':sushi:', to: '🍣'},
                            {from: ':ramen:', to: '🍜'},
                            {from: ':panda:', to: '🐼'},
                            {from: ':koala:', to: '🐨'},
                            {from: ':cat:', to: '🐱'},
                            {from: ':dog:', to: '🐶'},
                            {from: ':unicorn:', to: '🦄'},
                            {from: ':alien:', to: '👽'},
                            {from: ':ghost:', to: '👻'},
                            {from: ':robot:', to: '🤖'},
                            {from: ':zombie:', to: '🧟'},
                            {from: ':clown:', to: '🤡'},
                            {from: ':nerd:', to: '🤓'},
                            {from: ':rockon:', to: '🤘'},
                            {from: ':peace:', to: '✌️'},
                            {from: ':ok:', to: '👌'},
                            {from: ':sunglasses:', to: '😎'},
                            {from: ':dizzy:', to: '💫'},
                            {from: ':zap:', to: '⚡'},
                            {from: ':bomb:', to: '💣'},
                            {from: ':gun:', to: '🔫'},
                            {from: ':rose:', to: '🌹'},
                            {from: ':sunflower:', to: '🌻'},
                            {from: ':tulip:', to: '🌷'},
                            {from: ':cactus:', to: '🌵'},
                            {from: ':moon:', to: '🌙'},
                            {from: ':star2:', to: '🌟'},
                            {from: ':cloud:', to: '☁️'},
                            {from: ':rain:', to: '🌧️'},
                            {from: ':snowflake:', to: '❄️'},
                            {from: ':umbrella:', to: '☔'},
                            {from: ':fireworks:', to: '🎆'},
                            {from: ':balloon:', to: '🎈'},
                            {from: ':gift:', to: '🎁'},
                            {from: ':birthday:', to: '🎂'},
                            {from: ':music:', to: '🎶'},
                            {from: ':guitar:', to: '🎸'},
                            {from: ':film:', to: '🎬'},
                            {from: ':microphone:', to: '🎤'},
                            {from: ':headphones:', to: '🎧'},
                            {from: ':camera:', to: '📷'},
                            {from: ':video:', to: '🎥'},
                            {from: ':computer:', to: '💻'},
                            {from: ':phone:', to: '📱'},
                            {from: ':clock:', to: '🕰️'},
                            {from: ':money:', to: '💰'},
                            {from: ':thumbsup:', to: '👍'},
                            {from: ':thumbsdown:', to: '👎'},

                            // You can also define patterns using regular expressions.
                            // Note: The pattern must end with `$` and all its fragments must be wrapped
                            // with capturing groups.
                            // The following rule replaces ` "foo"` with ` «foo»`.
                            //{
                            //  from: /(^|\s)(")([^"]*)(")$/,
                            //  to: [ null, '«', null, '»' ]
                            //},

                            // Finally, you can define `to` as a callback.
                            // This (naive) rule will auto-capitalize the first word after a period, question mark, or an exclamation mark.
                            {
                                from: /([.?!] )([a-z])$/,
                                to: matches => [null, matches[1].toUpperCase()]
                            }
                        ],
                    }
                }
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    editor.sourceElement.value = editor.getData();
                });
            })
        ;
    }

    DisallowNestingBlockQuotes = (editor) => {
        editor.model.schema.addChildCheck((context, childDefinition) => {
            if (context.endsWith('blockQuote') && childDefinition.name === 'blockQuote') {
                return false;
            }
        });
    }

    RemoveFormatLinks = (editor) => {
        // Extend the editor schema and mark the "linkHref" model attribute as formatting.
        editor.model.schema.setAttributeProperties('linkHref', {
            isFormatting: true
        });
    }
}
