import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

export default function Edit() {
    return (
        <p { ...useBlockProps() }>
            { __(
                'Block with Dynamic Rendering – hello from the editor!',
                'multisite-language-switcher'
            ) }
        </p>
    );
}