/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import {__} from '@wordpress/i18n';

import classnames from 'classnames';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
    useBlockProps,
    useInnerBlocksProps,
    InspectorControls
} from '@wordpress/block-editor';

import {getBlockType} from '@wordpress/blocks';

import {addFilter} from '@wordpress/hooks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import {PanelBody, PanelRow, TextControl, ColorPicker, Button} from "@wordpress/components";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit(props) {
    const {attributes, setAttributes, className} = props;
    const {fontSize, layout, style} = attributes;

    const blockProps = useBlockProps({
        className: classnames(className)
    });

    const ButtonEdit = getBlockType('core/button');

    const ALLOWED_BLOCKS = [ButtonEdit.name];

    const DEFAULT_BLOCK = {
        name: ButtonEdit.name,
        attributesToCopy: [
            'backgroundColor',
            'border',
            'className',
            'fontFamily',
            'fontSize',
            'gradient',
            'style',
            'textColor',
            'width'
        ]
    };

    const innerBlockProps = useInnerBlocksProps(blockProps, {
        allowedBlocks: ALLOWED_BLOCKS,
        defaultBlock: DEFAULT_BLOCK,
        directInsert: true,
        template: [
            [
                ButtonEdit.name,
                {className: 'wcSheerIDButton'}
            ],
        ],
        templateInsertUpdatesSelection: true,
        orientation: layout?.orientation ?? 'horizontal',
    });

    const updateAttribute = (key, value) => {
        setAttributes({[key]: value});
    }

    return (
        <>
            <div {...innerBlockProps}>

            </div>
            <InspectorControls>
                <PanelBody title={'SheerID Settings'}>
                    <PanelRow>
                        <TextControl
                            label={'Program ID'}
                            value={attributes.program}
                            onChange={value => updateAttribute('program', value)}/>
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={'Loading Text'}
                            value={attributes.loading_text}
                            onChange={value => updateAttribute('loading_text', value)}/>
                    </PanelRow>
                    <PanelRow>
                        <TextControl
                            label={'Coupon (optional)'}
                            value={attributes.coupon}
                            onChange={value => updateAttribute('coupon', value)}/>
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
        </>
    );
}

addFilter('blocks.getSaveContent.extraProps', 'wcSheerID', (extraProps, blockType, attributes) => {
    return extraProps;
});
