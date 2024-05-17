import { useBlockProps } from '@wordpress/block-editor';
import { ServerSideRender, TextControl } from '@wordpress/components';

export default function edit() {
    const blockProps = useBlockProps();
    return (
        <>
            <ServerSideRender
                block="lloc/msls-widget-block"
                attributes={blockProps.attributes}
            />
            <InspectorControls>
                <TextControl
                    label="Title"
                    value={blockProps.attributes.title}
                    onChange={(value) => {
                        blockProps.setAttributes({ title: value });
                    }}
                />
            </InspectorControls>
        </>
    );
}