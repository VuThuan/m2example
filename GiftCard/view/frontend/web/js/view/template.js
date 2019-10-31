/**
 * Bdcrops
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Bdcrops.com license that is
 * available through the world-wide-web at this URL:
 * https://www.bdcrops.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Bdcrops
 * @package     Bdcrops_GiftCard
 * @copyright   Copyright (c) Bdcrops (https://www.bdcrops.com/)
 * @license     https://www.bdcrops.com/LICENSE.txt
 */

/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'underscore',
        'ko',
        'uiComponent',
        'Bdcrops_GiftCard/js/model/product',
        'mage/template',
        'Magento_Ui/js/modal/alert',
        'rjsResolver',
        'mage/translate',
        'jquery/file-uploader',
        'bdcrops/core/owl.carousel'
    ],
    function ($, _, ko, Component, giftCard, mageTemplate, alert, resolver, $t, $owl) {
        'use strict';

        var config = window.giftCardInformation,
            configureData = window.configureData,
            templates = config.template;

        return Component.extend({
            defaults: {
                template: 'Bdcrops_GiftCard/product/gallery'
            },
            activeTemplateId: ko.observable(),
            activeTemplate: giftCard.activeTemplate,
            activeImageSrc: giftCard.activeImage,
            uploadedImages: ko.observableArray(),
            previewStyle: ko.observable(),
            previewWidth: ko.observable('80%'),
            from: giftCard.from,
            to: giftCard.to,
            message: giftCard.message,
            amount: giftCard.activeAmount,
            uploadUrl: config.information.fileUploadUrl,

            /**
             * Init component
             */
            initialize: function () {
                this._super();

                this.initCardFields();
                this.initConfigureData();

                return this;
            },

            initSlide: function () {
                var container = $('.giftcard-template-choose-images');
                $('.owl-stage-outer').remove();
                if (container.children('.image.item-template').length) {
                    container.children('.owl-splatage-outer').remove();
                }

                container.trigger('destroy.owl.carousel');
                container.find('.owl-stage-outer').children().unwrap();

                if (!container.hasClass('owl-carousel owl-theme owl-center')) {
                    container.addClass('owl-carousel owl-theme owl-center');
                }

                container.owlCarousel({
                    loop: false,
                    margin: 10,
                    items: 5,
                    center: true,
                    dots: false,
                    nav: true,
                    navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
                });
            },

            initEvent: function () {
                var self = this;
                $('#giftcard-template-select').on('change', function () {
                    self.activeTemplateId(parseInt(this.value));
                });
                $('#giftcard-template-container-loader').remove();
            },

            initObservable: function () {
                this._super()
                    .observe({
                        fields: [],
                        cardStyle: '',
                        image: ''
                    });

                var self = this;

                this.activeTemplateId.subscribe(function (templateId) {
                    var activeTemplate = _.find(self.templates, function (template) {
                        return parseInt(template.id) === templateId;
                    });
                    self.activeTemplate(activeTemplate);
                    if (self.templates.length > 5) {
                        self.changeDesign(activeTemplate);
                    }
                    if (activeTemplate.images.length) {
                        self.activeImageSrc(activeTemplate.images[0].file);
                        self.image(activeTemplate.images[0].src);
                    }

                    self.fields($.map(activeTemplate.design, function (value, index) {
                        switch (index) {
                            case 'from':
                                value.value = ko.computed(function () {
                                    var from = (typeof self.from() !== 'undefined') ? self.from() : '';
                                    return $.mage.__('From: ') + from;
                                });
                                break;
                            case 'to':
                                value.value = ko.computed(function () {
                                    var to = (typeof self.to() !== 'undefined') ? self.to() : '';
                                    return $.mage.__('To: ') + to;
                                });
                                break;
                            case 'message':
                                value.value = self.message;
                                break;
                            case 'value':
                                value.value = ko.computed(function () {
                                    if (typeof self.amount() !== 'undefined') {
                                        return self.amount().amountFormatted;
                                    }
                                    return 0;
                                });
                                break;
                            case 'image':
                                value.src = self.image;
                                break;
                        }

                        return [value];
                    }));

                    if (activeTemplate.card.css['font-family'] === undefined) {
                        activeTemplate.card.css['font-family'] = activeTemplate.font;
                    }
                    self.cardStyle(activeTemplate.card.css);
                    self.calculateScale();
                });

                $(window).resize(function () {
                    self.calculateScale();
                });

                return this;
            },

            /**
             * computed variable for show gift card template
             */
            initCardFields: function () {
                // Convert templates to array
                this.templates = $.map(templates, function (value, index) {
                    return [value];
                });

                if (!this.templates.length) {
                    return this;
                }

                this.activeTemplateId(this.templates[0].id);
                resolver(this.initUploadFile.bind(this));
            },

            /**
             * Init saved data
             * @returns {exports}
             */
            initConfigureData: function () {
                if (configureData.hasOwnProperty('template')) {
                    var templateId = parseInt(configureData.template),
                        template = _.find(this.templates, function (templateTmp) {
                            return parseInt(templateTmp.id) === templateId;
                        });
                    if (typeof template === 'undefined') {
                        return this;
                    }

                    this.changeDesign(template);

                    if (configureData.hasOwnProperty('image')) {
                        var imageSrc = configureData.image;
                        if (imageSrc.indexOf('.tmp') !== -1 && configureData.hasOwnProperty('imageSrc')) {
                            var data = {
                                src: configureData.imageSrc,
                                file: imageSrc
                            };
                            this.uploadedImages.push(data);
                            this.changeImages(data);
                        } else {
                            if (template.images.length) {
                                var image = _.find(template.images, function (imageTmp) {
                                    return imageTmp.file === imageSrc;
                                });

                                if (typeof image !== 'undefined') {
                                    this.changeImages(image);
                                }
                            }
                        }
                    }
                }
            },

            /**
             * Init upload file button
             */
            initUploadFile: function () {
                var self = this,
                    uploader = $('#image-uploader input[type=file]'),
                    uploadElementSpinner = $('#image-uploader-spinner');

                uploader.fileupload({
                    process: [{
                        action: 'load',
                        fileTypes: /^image\/(gif|jpeg|png)$/,
                        maxFileSize: 1048576 //1MB
                    }],
                    dataType: 'json',
                    sequentialUploads: true,
                    add: function (e, data) {
                        uploadElementSpinner.show();
                        $(this).fileupload('process', data).done(function () {
                            data.submit();
                        });
                    },
                    done: function (e, data) {
                        if (data.result && !data.result.error) {
                            data.result.src = data.result.url;
                            self.uploadedImages.push(data.result);
                            self.changeImages(data.result);
                        } else {
                            alert({content: $.mage.__('We don\'t recognize or support this file extension type.')});
                        }
                        uploadElementSpinner.hide();
                    }
                });
            },

            /**
             * @param template
             * @param data
             */
            changeDesign: function (template) {
                resolver(this.initUploadFile.bind(this));

                this.activeTemplateId(parseInt(template.id));

                this.initSlide();

                if (this.activeTemplate().images.length) {
                    this.activeImageSrc(this.activeTemplate().images[0].file);
                    this.image(this.activeTemplate().images[0].src);
                } else {
                    this.activeImageSrc('');
                }
            },

            /**
             *
             * @param image
             */
            changeImages: function (image) {
                this.activeImageSrc(image.file);
                this.image(image.src);
            },

            /**
             * Check product use template or not
             */
            templatesLength: function () {
                return this.templates.length;
            },

            /**
             * Calculate scale for template image show on product image area
             * @returns {exports}
             */
            calculateScale: function () {
                var maxWidth = parseInt($('#giftcard-template-container').width()) * 0.8,
                    rate = Math.min(maxWidth / parseInt(this.cardStyle().width), 350 / parseInt(this.cardStyle().height), 1);

                this.previewStyle({
                    transform: 'scale(' + rate + ')',
                    height: Math.max(rate * parseInt(this.cardStyle().height), 350) + 'px',
                });

                this.previewWidth(rate * parseInt(this.cardStyle().width) + 'px');

                return this;
            },

            isImageField: function (key) {
                return $.inArray(key, ['image', 'logo', 'barcode']) !== -1;
            }
        });
    }
);

