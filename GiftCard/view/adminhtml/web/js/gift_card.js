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

/**************************** GIFTCARD PRODUCT **************************/
define([
    'jquery',
    'mage/template',
    'Bdcrops_GiftCard/js/model/product',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'rjsResolver',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'prototype',
    'jquery/fileUploader/jquery.fileupload-fp'
], function ($, mageTemplate, GiftCard, utils, _, resolver, alert) {

    if (typeof GiftCardProduct == 'undefined') {
        window.GiftCardProduct = {};
    }

    GiftCardProduct.Config = Class.create();
    GiftCardProduct.Config.prototype = {
        Element: {
            container: '#giftcard-information-container',
            amount: '.giftcard-amount',
            delivery: '.giftcard-information-delivery',
            deliveryField: '.giftcard-information-delivery-content',
            openAmount: '.giftcard-design-open-amount',
            templatesShort: '.template-length-less-5',
            templateOption: '.giftcard-design-button',
            templateContent: '.giftcard-template-setting-content',
            templateImages: '.giftcard-template-choose-images',
            imageUploaded: '#giftcard-template-upload-image',
            qtyCore: '#product_composite_configure_input_qty'
        },

        HiddenField: {
            template: '#template',
            delivery: '#delivery',
            rangeAmount: '#range_amount'
        },

        initialize: function (config) {
            var self = this;

            this.productData = config.information;
            this.templates = config.template;
            this.activeTemplate = this.templates[_.keys(this.templates)[0]];
            this.deliveries = this.productData.delivery;
            this.activeDelivery = this.deliveries[_.keys(this.deliveries)[0]];
            this.viewInitialize = config.viewInitialize;
            this.maxLength = this.productData.messageMaxChar;
            this.uploadedImages = [];
            GiftCard.setProductId(this.productData.productId);

            /**
             * Binding data
             */
            $(this.HiddenField.delivery).on('change', function () {
                var value = parseInt($(this).val()),
                    delivery = _.find(self.deliveries, function (deliveryTmp) {
                        return deliveryTmp.key === value;
                    });

                self.initDeliveryFields(delivery);
            });

            $(this.HiddenField.template).on('change', function () {
                var value = parseInt($(this).val()),
                    delivery = _.find(self.deliveries, function (deliveryTmp) {
                        return deliveryTmp.key === parseInt($(self.HiddenField.delivery).val());
                    }),
                    template = _.find(self.templates, function (deliveryTmp) {
                        return deliveryTmp.id === value;
                    });

                self.initDeliveryFields(delivery, template);
            });

            /**
             * Init data
             */
            this.initGiftCardData();

            resolver(this.initCalendar.bind(this));
        },

        /**
         * Init Amount
         */
        initGiftCardData: function () {
            var self = this;
            /**
             * Init templates
             */
            var designTemplate = '<span class="giftcard-design-button-container">' +
                '<button type="button" ' +
                'class="giftcard-design-button" ' +
                'id="giftcard-design-button-{{tmpId}}" ' +
                'data-template-id="{{tmpId}}">{{tmpName}}' +
                '</button>' +
                '</span>';

            var templateShort = this.Element.templatesShort;
            if (!self.viewInitialize) {
                $.each(this.templates, function (index, template) {
                    $(templateShort).append(designTemplate
                        .replace(/{{tmpId}}/g, template.id)
                        .replace('{{tmpName}}', template.name)
                    );
                })
            }

            var templateOption = $(this.Element.templateOption);

            templateOption.on('click', function () {
                var selectingTmpId = $(this).data('template-id');
                var currentTmpId = GiftCard.activeTemplate.id;
                var template = self.templates[selectingTmpId];
                var currentActiveTmpElm = $('#giftcard-design-button-' + currentTmpId);
                var selectingTmpElm = $('#giftcard-design-button-' + selectingTmpId);

                if (typeof template !== 'undefined') {
                    GiftCard.setTemplate(template);
                    $(currentActiveTmpElm).parent().removeClass('active');
                    $(selectingTmpElm).parent().addClass('active');
                    self.initTemplateImages();
                    if (GiftCard.activeTemplate.canUpload) {
                        self.initTemplateUploader();
                    }
                }
            });

            templateOption.first().trigger('click');

            /**
             * Init amounts
             */
            this.amounts = [];
            var amountsEle = $(this.Element.amount);
            $.each(this.productData.amounts, function (index, amount) {
                var amountConverted = self.convertPrice(amount.amount);
                self.amounts[index] = {
                    baseValue: amount.amount,
                    value: amountConverted,
                    amountFormatted: self.formatPrice(amountConverted),
                    price: self.convertPrice(amount.price),
                    priceFormatted: self.convertAndFormat(amount.price)
                };

                if (amountsEle && !self.viewInitialize) {

                    var amountHtml = '<li class="giftcard-design-button-container">\n' +
                        '    <button class="giftcard-design-button amount-option" type="button" data-amounts-index="{{amountIndex}}">\n' +
                        '        <span>{{amountValue}}</span>\n' +
                        '    </button>\n' +
                        '</li>';
                    amountsEle.append(amountHtml
                        .replace('{{amountIndex}}', index)
                        .replace('{{amountValue}}', self.amounts[index].amountFormatted)
                    );
                }
            });

            /**
             * Amount range
             */
            if (this.productData.openAmount) {
                this.minAmount = this.convertPrice(this.productData.openAmount.min);
                this.minAmount = Math.max(0, this.minAmount);
                this.maxAmount = this.convertPrice(this.productData.openAmount.max);
                this.maxAmount = Math.max(this.minAmount, this.maxAmount);

                if (!self.viewInitialize) {
                    var amountInputHtml = '<li class="giftcard-design-input-container">\n' +
                        '    <input type="text" class="giftcard-design-open-amount" placeholder="Enter Amount">\n' +
                        '</li>';
                    amountsEle.append(amountInputHtml);
                }

                $(this.Element.openAmount)
                    .on('click', function () {
                        $(this).focus();
                    })
                    .on('change', function () {
                        self.validateOpenAmount($(this));
                        self.activeElement($(this));
                    });
            }

            var amountOption = $(".amount-option");

            amountOption.on("click", function () {
                self.changeAmount(self.amounts[$(this).data('amounts-index')]);
                self.activeElement($(this));
            });

            this.changeAmount(this.amounts[0]);
            amountOption.first().trigger("click");

            /**
             * Init delivery
             */
            if (this.deliveries.length) {
                if (!self.viewInitialize) {
                    $(this.Element.delivery).show() //added in template
                    // .append($('<div/>', {
                    //     class: 'giftcard-field-label'
                    // }).html("Delivery"))
                    // .append($('<div/>', {
                    //     class: 'giftcard-field-wrapper'
                    // }).html($('<ul/>', {
                    //     class: 'giftcard-delivery'
                    // })))
                    ;

                    var deliveryHtml = '<li class="giftcard-design-button-container">\n' +
                        '    <button class="giftcard-design-button delivery-option" type="button" data-delivery-index="{{deliveryIndex}}">\n' +
                        '        <span>{{deliveryLabel}}</span>\n' +
                        '    </button>\n' +
                        '</li>';

                    this.deliveries.forEach(function (element, index) {
                        $('.giftcard-delivery').append(deliveryHtml
                            .replace('{{deliveryIndex}}', index)
                            .replace('{{deliveryLabel}}', element.label)
                        );
                    });
                }

                var deliveryOption = $(".delivery-option");

                deliveryOption.on("click", function () {
                    $(self.HiddenField.delivery).val(self.deliveries[$(this).data("delivery-index")].key).trigger("change");
                    deliveryOption.closest("li").removeClass("active");
                    $(this).closest("li").addClass("active");
                });

                deliveryOption.first().trigger("click");
            }

            return this;
        },

        /**
         * Init template uploader
         */
        initTemplateUploader: function () {
            var self = this,
                container = $(this.Element.templateContent);

            container.children().remove('.giftcard-template-upload');

            var uploadTemplateHtml = '<div class="giftcard-template-upload">\n' +
                '    <lable for="giftcard-template-upload-image">' + $.mage.__("Or upload your photo (.gif, .jpg, .png).") + '</lable>\n' +
                '    <div id="giftcard-template-upload-image" class="giftcard-template-upload-image">\n' +
                '        <div class="image item-template image-placeholder">\n' +
                '            <div class="uploader" id="image-uploader">\n' +
                '                <div class="fileinput-button form-buttons button"><input id="fileupload" type="file" name="image" title="' + $.mage.__("Browse to find or drag image here") + '" data-url="' + self.productData.fileUploadUrl + '" multiple="multiple"></div>\n' +
                '                <div id="image-uploader-spinner" class="file-row"></div>\n' +
                '            </div>\n' +
                '            <div class="template-image-wrapper">\n' +
                '                <p class="image-placeholder-text">' + $.mage.__("Browse to find or drag image here") + '</p>\n' +
                '            </div>\n' +
                '        </div>\n' +
                '    </div>\n' +
                '</div>';
            container.append($(uploadTemplateHtml));

            $(this.Element.imageUploaded).on('click', '.image-uploaded', function () {
                GiftCard.setImage($(this).data('image-file'));
                $(this).addClass('active');
            });

            this.initUploadFile();

            return this;
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
                        GiftCard.setImage(data.result.file);
                        self.uploadedImages = data.result;
                        self.initUploadedImages();
                    } else {
                        alert({content: $.mage.__('We don\'t recognize or support this file extension type.')});
                    }
                    uploadElementSpinner.hide();
                }
            });
        },

        /**
         * Init uploaded image
         */
        initUploadedImages: function () {
            var image = this.uploadedImages;

            var active = (image.file == GiftCard.activeImage) ? ' active' : ''; //space to separate classes

            var imgHtml = '<div class="image item-template image-uploaded' + active + '" data-image-file="' + image.file + '">\n' +
                '<div class="template-image-wrapper"><img class="template-image" src="' + image.src + '"></div>\n' +
                '</div>';

            $(imgHtml).insertBefore('.image-placeholder');
        },

        /**
         * Init template image
         */
        initTemplateImages: function () {
            var container = $(this.Element.templateImages).html('');

            var itemTpl = '<div class="image item-template {{activeClass}}" data-image-index="{{dataIndex}}">\n' +
                '<div class="template-image-wrapper"><img class="template-image" src="{{imgSrc}}" alt="{{imgAlt}}"></div>\n' +
                '</div>';

            $.each(GiftCard.activeTemplate.images, function (index, image) {
                var activeClass = (GiftCard.activeImage == image.file) ? 'active' : '';
                var imgSrc = image.src;
                var imgAlt = (image.alt) ? image.alt : 'Gift Card Image';

                container.append(itemTpl
                    .replace('{{activeClass}}', activeClass)
                    .replace('{{dataIndex}}', index)
                    .replace('{{imgSrc}}', imgSrc)
                    .replace('{{imgAlt}}', imgAlt)
                );
            });

            var items = $('.image.item-template');

            items.on('click', function () {
                GiftCard.setImage(GiftCard.activeTemplate.images[$(this).data('image-index')].file);
                $(this).addClass('active');
            });

            if (!GiftCard.activeImage.length) {
                items.first().trigger('click');
            }

            return this;
        },

        /**
         * Apply change for input open amount
         */
        validateOpenAmount: function (input) {
            var self = this,
                openAmount = input.val();

            openAmount = Math.max(0, openAmount);

            if (this.minAmount && openAmount < this.minAmount) {
                openAmount = this.minAmount;
            }
            if (this.maxAmount && openAmount > this.maxAmount) {
                openAmount = this.maxAmount;
            }

            if (openAmount > 0) {
                var price = self.getPriceFromAmount(openAmount);

                $(this.HiddenField.rangeAmount).val(1);
                GiftCard.setAmount({
                    baseValue: self.convertPrice(openAmount, true),
                    value: openAmount,
                    amountFormatted: self.formatPrice(openAmount),
                    price: price,
                    priceFormatted: self.formatPrice(price)
                });
                input.val(openAmount);
            }
        },

        /**
         * Get delivery fields
         * @returns {{}}
         */
        initDeliveryFields: function (activeDelivery, activeTemplate) {
            activeDelivery = activeDelivery || this.activeDelivery;
            activeTemplate = activeTemplate || this.activeTemplate;

            var deliveryFields = {},
                templateFields = {};

            if (activeTemplate && activeTemplate.hasOwnProperty('design')) {
                templateFields = activeTemplate.design;
            }

            if (typeof templateFields.from !== 'undefined') {
                deliveryFields.from = {
                    type: 'input',
                    label: $.mage.__('From'),
                    name: 'from',
                    value: GiftCard.from,
                    placeHolder: $.mage.__('Enter sender name')
                };
            }
            if (typeof templateFields.to !== 'undefined') {
                deliveryFields.to = {
                    type: 'input',
                    label: $.mage.__('To'),
                    name: 'to',
                    value: GiftCard.to,
                    placeHolder: $.mage.__('Enter recipient name')
                };
            }

            if (typeof activeDelivery.fields !== 'undefined') {
                deliveryFields = _.extendOwn({}, deliveryFields, activeDelivery.fields);
            }

            if (typeof templateFields.message !== 'undefined') {
                deliveryFields.message = {
                    type: 'textarea',
                    label: $.mage.__('Message'),
                    name: 'message',
                    value: GiftCard.message,
                    remainingLabel: $.mage.__('characters remaining')
                };
            }

            if (this.productData.enableDeliveryDate) {
                deliveryFields.deliveryDate = {
                    type: 'input',
                    label: $.mage.__('Delivery Date'),
                    name: 'delivery_date',
                    class: 'validate-date'
                };
            }
            if (this.productData.timezone.enable) {
                this.timezoneList = this.timezoneList || _.values(this.productData.timezone.options);
                deliveryFields.timezone = {
                    type: 'select',
                    label: $.mage.__('Timezone'),
                    name: 'timezone',
                    value: this.productData.timezone.value,
                    options: this.timezoneList
                };
            }

            this.deliveryFields = _.values(deliveryFields);

            this.renderDeliveryFields();

            if (this.productData.enableDeliveryDate) {
                this.initCalendar();
            }
        },

        /**
         * Render fields
         */
        renderDeliveryFields: function () {
            if (typeof this.deliveryFields !== 'undefined') {
                var self = this,
                    container = $(this.Element.deliveryField);

                container.html('');

                $.each(this.deliveryFields, function (index, field) {
                    var wrapper = $('<div/>', {class: 'giftcard-field-wrapper'});
                    var child;

                    switch (field.type) {
                        case 'input':
                            child = $('<input/>', {
                                id: field.name,
                                type: 'text',
                                value: field.value,
                                name: field.name,
                                class: (field.class) ? field.class : '',
                                placeHolder: (field.placeHolder) ? field.placeHolder : ''
                            });
                            break;
                        case 'textarea':
                            child = $('<textarea/>', {
                                name: field.name,
                                value: field.value,
                                maxlength: (self.maxLength) ? self.maxLength : '',
                                placeHolder: (field.placeHolder) ? field.placeHolder : ''
                            });

                            break;
                        case 'label':
                            child = $('<p/>', {
                                id: 'delivery-field-' + field.name
                            }).html(field.value);
                            break;
                        case 'select':
                            child = $('<select/>', {
                                id: field.name,
                                name: field.name
                            });

                            $.each(field.options, function (key, option) {
                                var grandchild = $('<option/>', {
                                    value: option.value,
                                    text: option.label,
                                    selected: option.value == field.value
                                });

                                child.append(grandchild);
                            });

                            break;
                    }

                    if (typeof child !== 'undefined') {
                        wrapper.append(child);

                        if (field.name === 'message') {
                            wrapper.append($('<p/>', {
                                class: 'note'
                            }).html($('<span>', {}).html(self.remainChar() + ' ' + field.remainingLabel)));
                        }

                        /**
                         * Binding data for fields
                         */
                        child.on('keyup', function () {
                            var name = $(this).attr('name');
                            GiftCard.setFieldValue(name, $(this).val());

                            if (name === 'message') {
                                $("p.note span").html(self.remainChar() + ' ' + field.remainingLabel);
                            }
                        });
                    }

                    container.append($('<div/>', {
                        class: 'giftcard-information giftcard-information-delivery-field'
                    }).append($('<div/>', {
                        class: 'giftcard-field-label'
                    }).html(field.label))
                        .append(wrapper).append($('<div/>', {
                            class: 'clear'
                        })));
                });
            }
        },

        /**
         * Remain character
         */
        remainChar: function () {
            return this.maxLength - GiftCard.message.length;
        },

        /**
         * Init delivery date
         */
        initCalendar: function () {
            $('#delivery_date').calendar({
                changeYear: false,
                showWeek: false,
                minDate: +1,
                maxDate: "+1Y",
                dateFormat: $.datepicker.W3C
            });
        },

        /**
         * Active element
         * @param element
         */
        activeElement: function (element) {
            element.closest('ul').find('li').removeClass('active');
            element.closest('li').addClass('active');
        },

        /**
         * Apply change for amounts dropdown
         * @param amount
         */
        changeAmount: function (amount) {
            GiftCard.setAmount(amount);
            $(this.HiddenField.rangeAmount).val(0);
        },

        /**
         * Convert price to show
         *
         * @param value
         * @param toBase
         * @returns {number}
         */
        convertPrice: function (value, toBase) {
            if (typeof toBase !== 'undefined') {
                return parseFloat(value) / this.productData.currencyRate;
            }

            return parseFloat(value) * this.productData.currencyRate;
        },

        /**
         * Get open amount price
         * @param amount
         * @returns {number}
         */
        getPriceFromAmount: function (amount) {
            var openAmountRate = this.productData.openAmount.rate || 100;
            return amount * openAmountRate / 100;
        },

        /**
         * Format Price
         * @param value
         * @returns {*|String}
         */
        formatPrice: function (value) {
            return utils.formatPrice(value, this.productData.priceFormat);
        },

        /**
         * Convert and format
         * @param value
         * @returns {*|String}
         */
        convertAndFormat: function (value) {
            var convertValue = this.convertPrice(value);
            return this.formatPrice(convertValue);
        }
    };

});
