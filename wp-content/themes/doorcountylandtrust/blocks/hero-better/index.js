/**
 * DCLT Hero Block - Editor Component
 */
import { registerBlockType } from '@wordpress/blocks';
import {
    useBlockProps,
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
    RichText,
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    RangeControl,
    Button,
    TextControl,
    ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import metadata from './block.json';

registerBlockType(metadata.name, {
    edit: ({ attributes, setAttributes }) => {
        const {
            backgroundType,
            backgroundImage,
            backgroundVideo,
            headline,
            subheadline,
            overlayOpacity,
            primaryCTALabel,
            primaryCTAUrl,
            secondaryCTALabel,
            secondaryCTAUrl,
            showScrollIndicator,
            minHeight,
        } = attributes;

        const blockProps = useBlockProps({
            className: 'dclt-hero-editor',
        });

        // Background media preview
        const renderBackgroundPreview = () => {
            if (backgroundType === 'video' && backgroundVideo?.url) {
                return (
                    <video
                        autoPlay
                        loop
                        muted
                        playsInline
                        src={backgroundVideo.url}
                        style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                    />
                );
            } else if (backgroundType === 'image' && backgroundImage?.url) {
                return (
                    <img
                        src={backgroundImage.url}
                        alt=""
                        style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                    />
                );
            }
            return (
                <div
                    style={{
                        width: '100%',
                        height: '100%',
                        background: 'linear-gradient(to bottom right, #1F4421, #3A5A3C, #4A7C95)',
                    }}
                />
            );
        };

        return (
            <>
                <InspectorControls>
                    {/* Background Settings */}
                    <PanelBody title={__('Background Settings', 'dclt-theme')} initialOpen={true}>
                        <SelectControl
                            label={__('Background Type', 'dclt-theme')}
                            value={backgroundType}
                            options={[
                                { label: 'Image', value: 'image' },
                                { label: 'Video', value: 'video' },
                                { label: 'Gradient', value: 'gradient' },
                            ]}
                            onChange={(value) => setAttributes({ backgroundType: value })}
                        />

                        {backgroundType === 'image' && (
                            <MediaUploadCheck>
                                <MediaUpload
                                    onSelect={(media) =>
                                        setAttributes({
                                            backgroundImage: {
                                                id: media.id,
                                                url: media.url,
                                                alt: media.alt,
                                            },
                                        })
                                    }
                                    allowedTypes={['image']}
                                    value={backgroundImage?.id}
                                    render={({ open }) => (
                                        <div>
                                            <Button
                                                onClick={open}
                                                variant="secondary"
                                                style={{ marginBottom: '10px' }}
                                            >
                                                {backgroundImage?.url
                                                    ? __('Replace Background Image', 'dclt-theme')
                                                    : __('Select Background Image', 'dclt-theme')}
                                            </Button>
                                            {backgroundImage?.url && (
                                                <>
                                                    <img
                                                        src={backgroundImage.url}
                                                        alt=""
                                                        style={{ maxWidth: '100%', height: 'auto' }}
                                                    />
                                                    <Button
                                                        onClick={() =>
                                                            setAttributes({ backgroundImage: null })
                                                        }
                                                        isDestructive
                                                        variant="secondary"
                                                        style={{ marginTop: '10px' }}
                                                    >
                                                        {__('Remove Image', 'dclt-theme')}
                                                    </Button>
                                                </>
                                            )}
                                        </div>
                                    )}
                                />
                            </MediaUploadCheck>
                        )}

                        {backgroundType === 'video' && (
                            <MediaUploadCheck>
                                <MediaUpload
                                    onSelect={(media) =>
                                        setAttributes({
                                            backgroundVideo: {
                                                id: media.id,
                                                url: media.url,
                                            },
                                        })
                                    }
                                    allowedTypes={['video']}
                                    value={backgroundVideo?.id}
                                    render={({ open }) => (
                                        <div>
                                            <Button
                                                onClick={open}
                                                variant="secondary"
                                                style={{ marginBottom: '10px' }}
                                            >
                                                {backgroundVideo?.url
                                                    ? __('Replace Background Video', 'dclt-theme')
                                                    : __('Select Background Video', 'dclt-theme')}
                                            </Button>
                                            {backgroundVideo?.url && (
                                                <>
                                                    <video
                                                        src={backgroundVideo.url}
                                                        controls
                                                        style={{ maxWidth: '100%', height: 'auto' }}
                                                    />
                                                    <Button
                                                        onClick={() =>
                                                            setAttributes({ backgroundVideo: null })
                                                        }
                                                        isDestructive
                                                        variant="secondary"
                                                        style={{ marginTop: '10px' }}
                                                    >
                                                        {__('Remove Video', 'dclt-theme')}
                                                    </Button>
                                                </>
                                            )}
                                        </div>
                                    )}
                                />
                            </MediaUploadCheck>
                        )}

                        <RangeControl
                            label={__('Overlay Opacity', 'dclt-theme')}
                            value={overlayOpacity}
                            onChange={(value) => setAttributes({ overlayOpacity: value })}
                            min={0}
                            max={1}
                            step={0.1}
                        />
                    </PanelBody>

                    {/* CTA Settings */}
                    <PanelBody title={__('Call-to-Action Buttons', 'dclt-theme')} initialOpen={false}>
                        <h3>{__('Primary CTA', 'dclt-theme')}</h3>
                        <TextControl
                            label={__('Button Label', 'dclt-theme')}
                            value={primaryCTALabel}
                            onChange={(value) => setAttributes({ primaryCTALabel: value })}
                        />
                        <TextControl
                            label={__('Button URL', 'dclt-theme')}
                            value={primaryCTAUrl}
                            onChange={(value) => setAttributes({ primaryCTAUrl: value })}
                            type="url"
                        />

                        <hr style={{ margin: '20px 0' }} />

                        <h3>{__('Secondary CTA', 'dclt-theme')}</h3>
                        <TextControl
                            label={__('Button Label', 'dclt-theme')}
                            value={secondaryCTALabel}
                            onChange={(value) => setAttributes({ secondaryCTALabel: value })}
                        />
                        <TextControl
                            label={__('Button URL', 'dclt-theme')}
                            value={secondaryCTAUrl}
                            onChange={(value) => setAttributes({ secondaryCTAUrl: value })}
                            type="url"
                        />
                    </PanelBody>

                    {/* Display Settings */}
                    <PanelBody title={__('Display Settings', 'dclt-theme')} initialOpen={false}>
                        <ToggleControl
                            label={__('Show Scroll Indicator', 'dclt-theme')}
                            checked={showScrollIndicator}
                            onChange={(value) => setAttributes({ showScrollIndicator: value })}
                        />
                        <TextControl
                            label={__('Minimum Height', 'dclt-theme')}
                            value={minHeight}
                            onChange={(value) => setAttributes({ minHeight: value })}
                            help={__('e.g., 600px, 50vh', 'dclt-theme')}
                        />
                    </PanelBody>
                </InspectorControls>

                <div {...blockProps}>
                    <div
                        style={{
                            position: 'relative',
                            minHeight: minHeight,
                            height: '600px',
                            display: 'flex',
                            alignItems: 'center',
                            overflow: 'hidden',
                        }}
                    >
                        {/* Background */}
                        <div style={{ position: 'absolute', inset: 0, zIndex: 0 }}>
                            {renderBackgroundPreview()}
                            <div
                                style={{
                                    position: 'absolute',
                                    inset: 0,
                                    background:
                                        'linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0.4), rgba(0,0,0,0.6))',
                                    opacity: overlayOpacity,
                                }}
                            />
                        </div>

                        {/* Content */}
                        <div
                            style={{
                                position: 'relative',
                                zIndex: 10,
                                width: '100%',
                                maxWidth: '1280px',
                                margin: '0 auto',
                                padding: '0 1rem',
                            }}
                        >
                            <div style={{ maxWidth: '56rem' }}>
                                <RichText
                                    tagName="h1"
                                    value={headline}
                                    onChange={(value) => setAttributes({ headline: value })}
                                    placeholder={__('Enter headline...', 'dclt-theme')}
                                    style={{
                                        color: 'white',
                                        marginBottom: '1.5rem',
                                        fontSize: '3rem',
                                        fontWeight: '600',
                                        lineHeight: '1.2',
                                    }}
                                />

                                <RichText
                                    tagName="p"
                                    value={subheadline}
                                    onChange={(value) => setAttributes({ subheadline: value })}
                                    placeholder={__('Enter subheadline...', 'dclt-theme')}
                                    style={{
                                        color: 'rgba(255,255,255,0.95)',
                                        marginBottom: '2rem',
                                        maxWidth: '42rem',
                                        fontSize: '1.25rem',
                                        lineHeight: '1.6',
                                    }}
                                />

                                <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
                                    {primaryCTALabel && (
                                        <div
                                            style={{
                                                padding: '0.75rem 1.5rem',
                                                backgroundColor: '#1F4421',
                                                color: 'white',
                                                borderRadius: '0.375rem',
                                                fontWeight: '500',
                                            }}
                                        >
                                            {primaryCTALabel}
                                        </div>
                                    )}
                                    {secondaryCTALabel && (
                                        <div
                                            style={{
                                                padding: '0.75rem 1.5rem',
                                                backgroundColor: 'rgba(255,255,255,0.1)',
                                                color: 'white',
                                                border: '1px solid rgba(255,255,255,0.3)',
                                                borderRadius: '0.375rem',
                                                fontWeight: '500',
                                                backdropFilter: 'blur(4px)',
                                            }}
                                        >
                                            {secondaryCTALabel}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Scroll Indicator */}
                        {showScrollIndicator && (
                            <div
                                style={{
                                    position: 'absolute',
                                    bottom: '2rem',
                                    left: '50%',
                                    transform: 'translateX(-50%)',
                                    zIndex: 10,
                                }}
                            >
                                <div
                                    style={{
                                        width: '24px',
                                        height: '40px',
                                        border: '2px solid rgba(255,255,255,0.5)',
                                        borderRadius: '20px',
                                        display: 'flex',
                                        justifyContent: 'center',
                                        paddingTop: '8px',
                                    }}
                                >
                                    <div
                                        style={{
                                            width: '4px',
                                            height: '8px',
                                            backgroundColor: 'rgba(255,255,255,0.7)',
                                            borderRadius: '2px',
                                        }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </>
        );
    },

    save: () => {
        // Return null for server-side rendering
        return null;
    },
});
