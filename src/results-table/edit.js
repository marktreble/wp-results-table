/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Imports the InspectorControls component, which is used to wrap
 * the block's custom controls that will appear in in the Settings
 * Sidebar when the block is selected.
 *
 * Also imports the React hook that is used to mark the block wrapper
 * element. It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#inspectorcontrols
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Imports the necessary components that will be used to create
 * the user interface for the block's settings.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/panel/#panelbody
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/text-control/
 * @see https://developer.wordpress.org/block-editor/reference-guides/components/toggle-control/
 */
import { PanelBody, TextControl, Disabled, SelectControl } from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
//import './editor.scss';

import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { heading, folder, algo } = attributes;
	
	const blockProps = useBlockProps( {
		className: 'wz-dynamic-block',
	} )

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'results-table' ) }>
					<TextControl
						label={ __(
							'Block Title',
							'results-table'
						) }
						value={ heading }
						onChange={ ( value ) =>
							setAttributes( { heading: value } )
						}
					/>
					<TextControl
						label={ __(
							'Folder',
							'results-table'
						) }
						value={ folder }
						onChange={ ( value ) =>
							setAttributes( { folder: value } )
						}
					/>
					<SelectControl
						label={ __(
							'League Result Algorithm',
							'results-table'
						) }
						value={ algo }
						options={ [
							{ label: 'BMFA2018', value: 'BMFA2018' },
							{ label: 'BMFA2016', value: 'BMFA2016' },
							{ label: 'BMFA2015', value: 'BMFA2015' },
							{ label: 'BMFA2012', value: 'BMFA2012' },
							{ label: 'BMFA2000', value: 'BMFA2000' },
							{ label: 'Winter League', value: 'WL' }
						] }
						onChange={ ( value ) =>
							setAttributes( { algo: value } )
						}
					/>
				</PanelBody>				
			</InspectorControls>
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block={ metadata.name }
						skipBlockSupportAttributes
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}

