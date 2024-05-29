(function ($) {
    "use strict";

    let DIRECTION = null;

    function direction() {
        if (DIRECTION === null) {
            DIRECTION = getComputedStyle(document.body).direction;
        }

        return DIRECTION;
    }

    function isRTL() {
        return direction() === 'rtl';
    }


    /*
    // collapse
    */
    $(function () {
        $('[data-collapse]').each(function (i, element) {
            const collapse = element;
            const openedClass = $(element).data('collapse-opened-class');

            $('[data-collapse-trigger]', collapse).on('click', function () {
                const item = $(this).closest('[data-collapse-item]');
                const content = item.children('[data-collapse-content]');
                const itemParents = item.parents();

                itemParents.slice(0, itemParents.index(collapse) + 1).filter('[data-collapse-item]').css('height', '');

                if (item.is('.' + openedClass)) {
                    const startHeight = content.height();

                    content.css('height', startHeight + 'px');
                    content.height(); // force reflow
                    item.removeClass(openedClass);

                    content.css('height', '');
                } else {
                    const startHeight = content.height();

                    item.addClass(openedClass);

                    const endHeight = content.height();

                    content.css('height', startHeight + 'px');
                    content.height(); // force reflow
                    content.css('height', endHeight + 'px');
                }
            });

            $('[data-collapse-content]', collapse).on('transitionend', function (event) {
                if (event.originalEvent.propertyName === 'height') {
                    $(this).css('height', '');
                }
            });
        });
    });

    /*
    // .filter-price
    */
    $(function () {
        // Initialise le filtre de prix avec les valeurs de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        const priceRange = urlParams.get('price_range');
        let from, to;

        if (priceRange) {
            [from, to] = priceRange.split('..').map(Number);
        } else {
            from = $('.filter-price').data('from');
            to = $('.filter-price').data('to');
        }

        $('.filter-price').each(function (i, element) {
            const min = $(element).data('min');
            const max = $(element).data('max');
            const slider = element.querySelector('.filter-price__slider');

            noUiSlider.create(slider, {
                start: [from, to],
                connect: true,
                direction: isRTL() ? 'rtl' : 'ltr',
                range: {
                    'min': min,
                    'max': max
                }
            });

            const titleValues = [
                $(element).find('.filter-price__min-value')[0],
                $(element).find('.filter-price__max-value')[0]
            ];

            slider.noUiSlider.on('update', function (values, handle) {
                titleValues[handle].innerHTML = values[handle];
            });

            $('.filter-price__button').on('click', function () {
                const values = slider.noUiSlider.get();
                urlParams.set('price_range', values[0] + '..' + values[1]);
                window.location.href = '/search?' + urlParams.toString();
            });
        });

        // Initialise le filtre de saison avec les valeurs de l'URL
        const selectedSeason = urlParams.get('season');
        if (selectedSeason) {
            document.querySelector(`.season-filter[data-season="${selectedSeason}"]`).checked = true;
        }

        // Initialise le filtre de notation avec les valeurs de l'URL
        const selectedRating = urlParams.get('rating');
        if (selectedRating) {
            document.querySelector(`.rating-filter[data-rating="${selectedRating}"]`).checked = true;
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.season-filter').forEach(function (element) {
            element.addEventListener('change', function () {
                const season = this.dataset.season;
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('season', season);
                window.location.href = '/search?' + urlParams.toString();
            });
        });

        document.querySelectorAll('.rating-filter').forEach((element) => {
            element.addEventListener('change', function () {
                const rating = this.getAttribute('data-rating');
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('rating', rating);
                window.location.href = '/search?' + urlParams.toString();
            });
        });
    });




    /*
    // .product-gallery
    */
    const initProductGallery = function(element, layout) {
        layout = layout !== undefined ? layout : 'standard';

        const options = {
            dots: false,
            margin: 10,
            rtl: isRTL(),
        };
        const layoutOptions = {
            'product-sidebar': {
                responsive: {
                    1400: {items: 8, margin: 10},
                    1200: {items: 6, margin: 10},
                    992: {items: 8, margin: 10},
                    768: {items: 8, margin: 10},
                    576: {items: 6, margin: 10},
                    420: {items: 5, margin: 8},
                    0: {items: 4, margin: 8}
                },
            },
            'product-full': {
                responsive: {
                    1400: {items: 6, margin: 10},
                    1200: {items: 5, margin: 8},
                    992: {items: 7, margin: 10},
                    768: {items: 5, margin: 8},
                    576: {items: 6, margin: 8},
                    420: {items: 5, margin: 8},
                    0: {items: 4, margin: 8}
                }
            },
            quickview: {
                responsive: {
                    992: {items: 5},
                    520: {items: 6},
                    440: {items: 5},
                    340: {items: 4},
                    0: {items: 3}
                }
            },
        };

        const gallery = $(element);

        const image = gallery.find('.product-gallery__featured .owl-carousel');
        const carousel = gallery.find('.product-gallery__thumbnails .owl-carousel');

        image
            .owlCarousel({items: 1, dots: false, rtl: isRTL()})
            .on('changed.owl.carousel', syncPosition);

        carousel
            .on('initialized.owl.carousel', function () {
                carousel.find('.product-gallery__thumbnails-item').eq(0).addClass('product-gallery__thumbnails-item--active');
            })
            .owlCarousel($.extend({}, options, layoutOptions[layout]));

        carousel.on('click', '.owl-item', function(e){
            e.preventDefault();

            image.data('owl.carousel').to($(this).index(), 300, true);
        });

        gallery.find('.product-gallery__zoom').on('click', function() {
            openPhotoSwipe(image.find('.owl-item.active').index());
        });

        image.on('click', '.owl-item > a', function(event) {
            event.preventDefault();

            openPhotoSwipe($(this).closest('.owl-item').index());
        });

        function getIndexDependOnDir(index) {
            // we need to invert index id direction === 'rtl' because photoswipe do not support rtl
            if (isRTL()) {
                return image.find('.owl-item img').length - 1 - index;
            }

            return index;
        }

        function openPhotoSwipe(index) {
            const photoSwipeImages = image.find('.owl-item a').toArray().map(function(element) {
                const img = $(element).find('img')[0];
                const width = $(element).data('width') || img.naturalWidth;
                const height = $(element).data('height') || img.naturalHeight;

                return {
                    src: element.href,
                    msrc: element.href,
                    w: width,
                    h: height,
                };
            });

            if (isRTL()) {
                photoSwipeImages.reverse();
            }

            const photoSwipeOptions = {
                getThumbBoundsFn: index => {
                    const imageElements = image.find('.owl-item img').toArray();
                    const dirDependentIndex = getIndexDependOnDir(index);

                    if (!imageElements[dirDependentIndex]) {
                        return null;
                    }

                    const tag = imageElements[dirDependentIndex];
                    const width = tag.naturalWidth;
                    const height = tag.naturalHeight;
                    const rect = tag.getBoundingClientRect();
                    const ration = Math.min(rect.width / width, rect.height / height);
                    const fitWidth = width * ration;
                    const fitHeight = height * ration;

                    return {
                        x: rect.left + (rect.width - fitWidth) / 2 + window.pageXOffset,
                        y: rect.top + (rect.height - fitHeight) / 2 + window.pageYOffset,
                        w: fitWidth,
                    };
                },
                index: getIndexDependOnDir(index),
                bgOpacity: .9,
                history: false
            };

            const photoSwipeGallery = new PhotoSwipe($('.pswp')[0], PhotoSwipeUI_Default, photoSwipeImages, photoSwipeOptions);

            photoSwipeGallery.listen('beforeChange', () => {
                image.data('owl.carousel').to(getIndexDependOnDir(photoSwipeGallery.getCurrentIndex()), 0, true);
            });

            photoSwipeGallery.init();
        }

        function syncPosition (el) {
            let current = el.item.index;

            carousel
                .find('.product-gallery__thumbnails-item')
                .removeClass('product-gallery__thumbnails-item--active')
                .eq(current)
                .addClass('product-gallery__thumbnails-item--active');
            const onscreen = carousel.find('.owl-item.active').length - 1;
            const start = carousel.find('.owl-item.active').first().index();
            const end = carousel.find('.owl-item.active').last().index();

            if (current > end) {
                carousel.data('owl.carousel').to(current, 100, true);
            }
            if (current < start) {
                carousel.data('owl.carousel').to(current - onscreen, 100, true);
            }
        }
    };

    $(function() {
        $('.product').each(function () {
            const gallery = $(this).find('.product-gallery');

            if (gallery.length > 0) {
                initProductGallery(gallery[0], gallery.data('layout'));
            }
        });
    });

    /*
    // .product-tabs
    */
    $(function () {
        $('.product-tabs').each(function (i, element) {
            $('.product-tabs__list', element).on('click', '.product-tabs__item a', function (event) {
                event.preventDefault();

                const tab = $(this).closest('.product-tabs__item');
                const content = $('.product-tabs__pane' + $(this).attr('href'), element);

                if (content.length) {
                    $('.product-tabs__item').removeClass('product-tabs__item--active');
                    tab.addClass('product-tabs__item--active');

                    $('.product-tabs__pane').removeClass('product-tabs__pane--active');
                    content.addClass('product-tabs__pane--active');
                }
            });

            const currentTab = $('.product-tabs__item--active', element);
            const firstTab = $('.product-tabs__item:first', element);

            if (currentTab.length) {
                currentTab.trigger('click');
            } else {
                firstTab.trigger('click');
            }
        });
    });

    /*
    // .departments
    */
    $(function() {
        $('.departments__button').on('click', function(event) {
            event.preventDefault();

            $(this).closest('.departments').toggleClass('departments--open');
        });

        $(document).on('click', function (event) {
            $('.departments')
                .not($(event.target).closest('.departments'))
                .removeClass('departments--open');
        });
    });

    /*
    // .topbar__menu
    */
    $(function() {
        $('.topbar__menu-button').on('click', function() {
            $(this).closest('.topbar__menu').toggleClass('topbar__menu--open');
        });

        $(document).on('click', function (event) {
            $('.topbar__menu')
                .not($(event.target).closest('.topbar__menu'))
                .removeClass('topbar__menu--open');
        });
    });

    /*
    // .indicator (dropcart, account-menu)
    */
    $(function() {
        $('.indicator--trigger--click .indicator__button').on('click', function(event) {
            event.preventDefault();

            const dropdown = $(this).closest('.indicator');

            if (dropdown.is('.indicator--open')) {
                dropdown.removeClass('indicator--open');
            } else {
                dropdown.addClass('indicator--open');
            }
        });

        $(document).on('click', function (event) {
            $('.indicator')
                .not($(event.target).closest('.indicator'))
                .removeClass('indicator--open');
        });
    });

    /*
    // .layout-switcher
    */
    $(function () {
        $('.layout-switcher__button').on('click', function() {
            const layoutSwitcher = $(this).closest('.layout-switcher');
            const productsView = $(this).closest('.products-view');
            const productsList = productsView.find('.products-list');

            layoutSwitcher
                .find('.layout-switcher__button')
                .removeClass('layout-switcher__button--active')
                .removeAttr('disabled');

            $(this)
                .addClass('layout-switcher__button--active')
                .attr('disabled', '');

            productsList.attr('data-layout', $(this).attr('data-layout'));
            productsList.attr('data-with-features', $(this).attr('data-with-features'));
        });
    });

    /*
    // mobile search
    */
    $(function() {
        const mobileSearch = $('.mobile-header__search');

        if (mobileSearch.length) {
            $('.mobile-indicator--search .mobile-indicator__button').on('click', function() {
                if (mobileSearch.is('.mobile-header__search--open')) {
                    mobileSearch.removeClass('mobile-header__search--open');
                } else {
                    mobileSearch.addClass('mobile-header__search--open');
                    mobileSearch.find('.mobile-search__input')[0].focus();
                }
            });

            mobileSearch.find('.mobile-search__button--close').on('click', function() {
                mobileSearch.removeClass('mobile-header__search--open');
            });

            document.addEventListener('click', function(event) {
                if (!$(event.target).closest('.mobile-indicator--search, .mobile-header__search, .modal').length) {
                    mobileSearch.removeClass('mobile-header__search--open');
                }
            }, true);

            $('.mobile-search__vehicle-picker').on('click', function () {
                $('#vehicle-picker-modal').modal('show');
            });
        }
    });

    /*
    // vehicle-picker-modal
    */
    $(function() {
        $('.vehicle-picker-modal').closest('.modal').each(function() {
            const modal = $(this);

            modal.on('hidden.bs.modal', function() {
                modal.find('[data-panel]')
                    .removeClass('vehicle-picker-modal__panel--active')
                    .first()
                    .addClass('vehicle-picker-modal__panel--active');
            });

            modal.find('[data-to-panel]').on('click', function(event) {
                event.preventDefault();

                const toPanel = $(this).data('to-panel');
                const currentPanel = modal.find('.vehicle-picker-modal__panel--active');
                const nextPanel = modal.find('[data-panel="' + toPanel + '"]');

                currentPanel.removeClass('vehicle-picker-modal__panel--active');
                nextPanel.addClass('vehicle-picker-modal__panel--active');
            });

            modal.find('.vehicle-picker-modal__close, .vehicle-picker-modal__close-button').on('click', function () {
                modal.modal('hide');
            });
        });
    });

    /*
    // mobile-menu
    */
    $(function () {
        const body = $('body');
        const mobileMenu = $('.mobile-menu');
        const mobileMenuBody = mobileMenu.children('.mobile-menu__body');

        if (mobileMenu.length) {
            const open = function() {
                const bodyWidth = body.width();
                body.css('overflow', 'hidden');
                body.css('paddingRight', (body.width() - bodyWidth) + 'px');

                mobileMenu.addClass('mobile-menu--open');
            };
            const close = function() {
                body.css('overflow', 'auto');
                body.css('paddingRight', '');

                mobileMenu.removeClass('mobile-menu--open');
            };

            $('.mobile-header__menu-button').on('click', function() {
                open();
            });
            $('.mobile-menu__backdrop, .mobile-menu__close').on('click', function() {
                close();
            });
        }

        const panelsStack = [];
        let currentPanel = mobileMenuBody.children('.mobile-menu__panel');

        mobileMenu.on('click', '[data-mobile-menu-trigger]', function(event) {
            const trigger = $(this);
            const item = trigger.closest('[data-mobile-menu-item]');
            let panel = item.data('panel');

            if (!panel) {
                panel = item.children('[data-mobile-menu-panel]').children('.mobile-menu__panel');

                if (panel.length) {
                    mobileMenuBody.append(panel);
                    item.data('panel', panel);
                    panel.width(); // force reflow
                }
            }

            if (panel && panel.length) {
                event.preventDefault();

                panelsStack.push(currentPanel);
                currentPanel.addClass('mobile-menu__panel--hide');

                panel.removeClass('mobile-menu__panel--hidden');
                currentPanel = panel;
            }
        });
        mobileMenu.on('click', '.mobile-menu__panel-back', function() {
            currentPanel.addClass('mobile-menu__panel--hidden');
            currentPanel = panelsStack.pop();
            currentPanel.removeClass('mobile-menu__panel--hide');
        });
    });

    /*
    // off canvas filters
    */
    $(function () {
        const body = $('body');
        const sidebar = $('.sidebar');
        const offcanvas = sidebar.hasClass('sidebar--offcanvas--mobile') ? 'mobile' : 'always';
        const media = matchMedia('(max-width: 991px)');

        if (sidebar.length) {
            const open = function() {
                if (offcanvas === 'mobile' && !media.matches) {
                    return;
                }

                const bodyWidth = body.width();
                body.css('overflow', 'hidden');
                body.css('paddingRight', (body.width() - bodyWidth) + 'px');

                sidebar.addClass('sidebar--open');
            };
            const close = function() {
                body.css('overflow', 'auto');
                body.css('paddingRight', '');

                sidebar.removeClass('sidebar--open');
            };
            const onMediaChange = function() {
                if (offcanvas === 'mobile') {
                    if (!media.matches && sidebar.hasClass('sidebar--open')) {
                        close();
                    }
                }
            };

            if (media.addEventListener) {
                media.addEventListener('change', onMediaChange);
            } else {
                media.addListener(onMediaChange);
            }

            $('.filters-button').on('click', function() {
                open();
            });
            $('.sidebar__backdrop, .sidebar__close').on('click', function() {
                close();
            });
        }
    });

    /*
    // tooltips
    */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    /*
    // departments megamenu
    */
    $(function () {
        let currentItem = null;
        const container = $('.departments__menu-container');

        $('.departments__item').on('mouseenter', function() {
            if (currentItem) {
                const megamenu = currentItem.data('megamenu');

                if (megamenu) {
                    megamenu.removeClass('departments__megamenu--open');
                }

                currentItem.removeClass('departments__item--hover');
                currentItem = null;
            }

            currentItem = $(this).addClass('departments__item--hover');

            if (currentItem.is('.departments__item--submenu--megamenu')) {
                let megamenu = currentItem.data('megamenu');

                if (!megamenu) {
                    megamenu = $(this).find('.departments__megamenu');

                    currentItem.data('megamenu', megamenu);

                    container.append(megamenu);
                }

                megamenu.addClass('departments__megamenu--open');
            }
        });
        $('.departments__list-padding').on('mouseenter', function() {
            if (currentItem) {
                const megamenu = currentItem.data('megamenu');

                if (megamenu) {
                    megamenu.removeClass('departments__megamenu--open');
                }

                currentItem.removeClass('departments__item--hover');
                currentItem = null;
            }
        });
        $('.departments__body').on('mouseleave', function() {
            if (currentItem) {
                const megamenu = currentItem.data('megamenu');

                if (megamenu) {
                    megamenu.removeClass('departments__megamenu--open');
                }

                currentItem.removeClass('departments__item--hover');
                currentItem = null;
            }
        });
    });

    /*
    // main menu / megamenu
    */
    $(function () {
        const megamenuArea = $('.megamenu-area');

        $('.main-menu__item--submenu--megamenu').on('mouseenter', function() {
            const megamenu = $(this).children('.main-menu__submenu');
            const offsetParent = megamenu.offsetParent();

            if (isRTL()) {
                const position = Math.max(
                    megamenuArea.offset().left,
                    Math.min(
                        $(this).offset().left + $(this).outerWidth() - megamenu.outerWidth(),
                        megamenuArea.offset().left + megamenuArea.outerWidth() - megamenu.outerWidth()
                    )
                ) - offsetParent.offset().left;

                megamenu.css('left', position + 'px');
            } else {
                const position = Math.max(
                    0,
                    Math.min(
                        $(this).offset().left,
                        megamenuArea.offset().left + megamenuArea.outerWidth() - megamenu.outerWidth()
                    )
                ) - offsetParent.offset().left;

                megamenu.css('left', position + 'px');
            }
        });
    });


    /*
    // Checkout payment methods
    */
    $(function () {
        $('[name="checkout_payment_method"]').on('change', function () {
            const currentItem = $(this).closest('.payment-methods__item');

            $(this).closest('.payment-methods__list').find('.payment-methods__item').each(function (i, element) {
                const links = $(element);
                const linksContent = links.find('.payment-methods__item-container');

                if (element !== currentItem[0]) {
                    const startHeight = linksContent.height();

                    linksContent.css('height', startHeight + 'px');
                    links.removeClass('payment-methods__item--active');

                    links.height(); // force reflow
                    linksContent.css('height', '');
                } else {
                    const startHeight = linksContent.height();

                    links.addClass('payment-methods__item--active');

                    const endHeight = linksContent.height();

                    linksContent.css('height', startHeight + 'px');
                    links.height(); // force reflow
                    linksContent.css('height', endHeight + 'px');
                }
            });
        });

        $('.payment-methods__item-container').on('transitionend', function (event) {
            if (event.originalEvent.propertyName === 'height') {
                $(this).css('height', '');
            }
        });
    });


    /*
    // add-vehicle-modal
    */
    $(function () {
        $('.filter-vehicle__button button').on('click', function () {
            $('#add-vehicle-modal').modal('show');
        });
    });


    /*
    // Quickview
    */
    const quickview = {
        cancelPreviousModal: function() {},
        clickHandler: function() {
            const modal = $('#quickview-modal');
            const button = $(this);
            const doubleClick = button.is('.product-card__action--loading');

            quickview.cancelPreviousModal();

            if (doubleClick) {
                return;
            }

            button.addClass('product-card__action--loading');

            let xhr = null;
            const timeout = setTimeout(function() {
                const id = button.attr('id');////
                xhr = $.ajax({
                    url: '/quickview/'+id,
                    type: 'GET',
                    success: function(data) {
                        quickview.cancelPreviousModal = function() {};
                        button.removeClass('product-card__action--loading');
                        modal.html(data);
                        modal.find('.quickview__close').on('click', function() {
                            modal.modal('hide');
                        });
                        modal.modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            }, 1000);

            quickview.cancelPreviousModal = function() {
                button.removeClass('product-card__action--loading');

                if (xhr) {
                    xhr.abort();
                }
                clearTimeout(timeout);
            };
        }
    };

    $(function () {
        const modal = $('#quickview-modal');

        modal.on('shown.bs.modal', function() {
            modal.find('.product-gallery').each(function(i, gallery) {
                initProductGallery(gallery, $(this).data('layout'));
            });

            $('.input-number', modal).customNumber();
        });

        $('.product-card__action--quickview').on('click', function() {
            quickview.clickHandler.apply(this, arguments);
        });
    });


    /*
    // .block-products-carousel
    */
    $(function () {
        const carouselOptions = {
            'grid-4': {
                items: 4,
            },
            'grid-4-sidebar': {
                items: 4,
                responsive: {
                    1400: {items: 4},
                    1200: {items: 3},
                    992: {items: 3, margin: 16},
                    768: {items: 3, margin: 16},
                    576: {items: 2, margin: 16},
                    460: {items: 2, margin: 16},
                    0: {items: 1},
                }
            },
            'grid-5': {
                items: 5,
                responsive: {
                    1400: {items: 5},
                    1200: {items: 4},
                    992: {items: 4, margin: 16},
                    768: {items: 3, margin: 16},
                    576: {items: 2, margin: 16},
                    460: {items: 2, margin: 16},
                    0: {items: 1},
                }
            },
            'grid-6': {
                items: 6,
                margin: 16,
                responsive: {
                    1400: {items: 6},
                    1200: {items: 4},
                    992: {items: 4, margin: 16},
                    768: {items: 3, margin: 16},
                    576: {items: 2, margin: 16},
                    460: {items: 2, margin: 16},
                    0: {items: 1},
                }
            },
            'horizontal': {
                items: 4,
                responsive: {
                    1400: {items: 4, margin: 14},
                    992: {items: 3, margin: 14},
                    768: {items: 2, margin: 14},
                    0: {items: 1, margin: 14},
                }
            },
            'horizontal-sidebar': {
                items: 3,
                responsive: {
                    1400: {items: 3, margin: 14},
                    768: {items: 2, margin: 14},
                    0: {items: 1, margin: 14},
                }
            }
        };

        $('.block-products-carousel').each(function() {
            const block = $(this);
            const layout = $(this).data('layout');
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel(Object.assign({}, {
                dots: false,
                margin: 20,
                loop: true,
                rtl: isRTL()
            }, carouselOptions[layout]));

            $(this).find('.section-header__arrow--prev').on('click', function() {
                owlCarousel.trigger('prev.owl.carousel', [500]);
            });
            $(this).find('.section-header__arrow--next').on('click', function() {
                owlCarousel.trigger('next.owl.carousel', [500]);
            });

            let cancelPreviousGroupChange = function() {};

            $(this).find('.section-header__groups-button').on('click', function() {
                const carousel = block.find('.block-products-carousel__carousel');

                if ($(this).is('.section-header__groups-button--active')) {
                    return;
                }

                cancelPreviousGroupChange();

                $(this).closest('.section-header__groups').find('.section-header__groups-button').removeClass('section-header__groups-button--active');
                $(this).addClass('section-header__groups-button--active');

                carousel.addClass('block-products-carousel__carousel--loading');

                // timeout ONLY_FOR_DEMO! you can replace it with an ajax request
                let timer;
                timer = setTimeout(function() {
                    let items = block.find('.owl-carousel .owl-item:not(".cloned") .block-products-carousel__column');

                    /*** this is ONLY_FOR_DEMO! / start */
                    /**/ const itemsArray = items.get();
                    /**/ const newItemsArray = [];
                    /**/
                    /**/ while (itemsArray.length > 0) {
                        /**/     const randomIndex = Math.floor(Math.random() * itemsArray.length);
                        /**/     const randomItem = itemsArray.splice(randomIndex, 1)[0];
                        /**/
                        /**/     newItemsArray.push(randomItem);
                        /**/ }
                    /**/ items = $(newItemsArray);
                    /*** this is ONLY_FOR_DEMO! / end */

                    block.find('.owl-carousel')
                        .trigger('replace.owl.carousel', [items])
                        .trigger('refresh.owl.carousel')
                        .trigger('to.owl.carousel', [0, 0]);

                    $('.product-card__action--quickview', block).on('click', function() {
                        quickview.clickHandler.apply(this, arguments);
                    });

                    carousel.removeClass('block-products-carousel__carousel--loading');
                }, 1000);
                cancelPreviousGroupChange = function() {
                    // timeout ONLY_FOR_DEMO!
                    clearTimeout(timer);
                    cancelPreviousGroupChange = function() {};
                };
            });
        });
    });

    /*
    // .block-posts-carousel
    */
    $(function () {
        const defaultOptions = {
            dots: false,
            margin: 20,
            loop: true,
            rtl: isRTL()
        };
        const options = {
            grid: {
                items: 4,
                responsive: {
                    1400: {items: 4, margin: 20},
                    1200: {items: 3, margin: 20},
                    992: {items: 3, margin: 16},
                    768: {items: 2, margin: 16},
                    0: {items: 1, margin: 16},
                },
            },
            list: {
                items: 2,
                responsive: {
                    1400: {items: 2, margin: 20},
                    992: {items: 2, margin: 16},
                    0: {items: 1, margin: 16},
                },
            },
        };

        $('.block-posts-carousel').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');
            const layout = $(this).data('layout');

            owlCarousel.owlCarousel(Object.assign({}, defaultOptions, options[layout]));

            $(this).find('.section-header__arrow--prev').on('click', function() {
                owlCarousel.trigger('prev.owl.carousel', [500]);
            });
            $(this).find('.section-header__arrow--next').on('click', function() {
                owlCarousel.trigger('next.owl.carousel', [500]);
            });
        });
    });

    /*
    // .block-teammates
    */
    $(function () {
        $('.block-teammates').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel({
                dots: true,
                margin: 20,
                rtl: isRTL(),
                responsive: {
                    992: {items: 4},
                    768: {items: 3},
                    440: {items: 2},
                    0: {items: 1}
                }
            });
        });
    });

    /*
    // .block-reviews
    */
    $(function () {
        $('.block-reviews').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel({
                dots: true,
                margin: 20,
                items: 1,
                loop: true,
                rtl: isRTL()
            });
        });
    });

    /*
    // .block-zone
    */
    $(function () {
        $('.block-zone').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel({
                dots: false,
                margin: 20,
                loop: true,
                items: 4,
                rtl: isRTL(),
                responsive: {
                    1400: {items: 4, margin: 20},
                    992: {items: 3, margin: 16},
                    460: {items: 2, margin: 16},
                    0: {items: 1},
                }
            });

            $(this).find('.block-zone__arrow--prev').on('click', function() {
                owlCarousel.trigger('prev.owl.carousel', [500]);
            });
            $(this).find('.block-zone__arrow--next').on('click', function() {
                owlCarousel.trigger('next.owl.carousel', [500]);
            });

            let cancelPreviousTabChange = function() {};

            $(this).find('.block-zone__tabs-button').on('click', function() {
                const block = $(this).closest('.block-zone');
                const carousel = block.find('.block-zone__carousel');

                if ($(this).is('.block-zone__tabs-button--active')) {
                    return;
                }

                cancelPreviousTabChange();

                $(this).closest('.block-zone__tabs').find('.block-zone__tabs-button').removeClass('block-zone__tabs-button--active');
                $(this).addClass('block-zone__tabs-button--active');

                carousel.addClass('block-zone__carousel--loading');

                // timeout ONLY_FOR_DEMO! you can replace it with an ajax request
                let timer;
                timer = setTimeout(function() {
                    let items = block.find('.owl-carousel .owl-item:not(".cloned") .block-zone__carousel-item');

                    /*** this is ONLY_FOR_DEMO! / start */
                    /**/ const itemsArray = items.get();
                    /**/ const newItemsArray = [];
                    /**/
                    /**/ while (itemsArray.length > 0) {
                        /**/     const randomIndex = Math.floor(Math.random() * itemsArray.length);
                        /**/     const randomItem = itemsArray.splice(randomIndex, 1)[0];
                        /**/
                        /**/     newItemsArray.push(randomItem);
                        /**/ }
                    /**/ items = $(newItemsArray);
                    /*** this is ONLY_FOR_DEMO! / end */

                    block.find('.owl-carousel')
                        .trigger('replace.owl.carousel', [items])
                        .trigger('refresh.owl.carousel')
                        .trigger('to.owl.carousel', [0, 0]);

                    $('.product-card__action--quickview', block).on('click', function() {
                        quickview.clickHandler.apply(this, arguments);
                    });

                    carousel.removeClass('block-zone__carousel--loading');
                }, 1000);
                cancelPreviousTabChange = function() {
                    // timeout ONLY_FOR_DEMO!
                    clearTimeout(timer);
                    cancelPreviousTabChange = function() {};
                };
            });
        });
    });

    /*
    // initialize custom numbers
    */
    $(function () {
        $('.input-number').customNumber();
    });

    /*
    // header vehicle
    */
    $(function () {
        const input = $('.search__input');
        const suggestions = $('.search__dropdown--suggestions');
        const vehiclePicker = $('.search__dropdown--vehicle-picker');
        const vehiclePickerButton = $('.search__button--start');

        input.on('focus', function() {
            suggestions.addClass('search__dropdown--open');
        });
        input.on('blur', function() {
            suggestions.removeClass('search__dropdown--open');
        });

        vehiclePickerButton.on('click', function() {
            vehiclePickerButton.toggleClass('search__button--hover');
            vehiclePicker.toggleClass('search__dropdown--open');
        });

        vehiclePicker.on('transitionend', function(event) {
            if (event.originalEvent.propertyName === 'visibility' && vehiclePicker.is(event.target)) {
                vehiclePicker.find('.vehicle-picker__panel:eq(0)').addClass('vehicle-picker__panel--active');
                vehiclePicker.find('.vehicle-picker__panel:gt(0)').removeClass('vehicle-picker__panel--active');
            }
            if (event.originalEvent.propertyName === 'height' && vehiclePicker.is(event.target)) {
                vehiclePicker.css('height', '');
            }
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest('.search__dropdown--vehicle-picker, .search__button--start').length) {
                vehiclePickerButton.removeClass('search__button--hover');
                vehiclePicker.removeClass('search__dropdown--open');
            }
        });

        $('.vehicle-picker [data-to-panel]').on('click', function(event) {
            event.preventDefault();

            const toPanel = $(this).data('to-panel');
            const currentPanel = vehiclePicker.find('.vehicle-picker__panel--active');
            const nextPanel = vehiclePicker.find('[data-panel="' + toPanel + '"]');

            currentPanel.removeClass('vehicle-picker__panel--active');
            nextPanel.addClass('vehicle-picker__panel--active');
        });
    });

    /*
    // .block-sale
    */
    $(function () {
        $('.block-sale').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel({
                items: 5,
                dots: true,
                margin: 24,
                loop: true,
                rtl: isRTL(),
                responsive: {
                    1400: {items: 5},
                    1200: {items: 4},
                    992: {items: 4, margin: 16},
                    768: {items: 3, margin: 16},
                    576: {items: 2, margin: 16},
                    460: {items: 2, margin: 16},
                    0: {items: 1},
                },
            });

            $(this).find('.block-sale__arrow--prev').on('click', function() {
                owlCarousel.trigger('prev.owl.carousel', [500]);
            });
            $(this).find('.block-sale__arrow--next').on('click', function() {
                owlCarousel.trigger('next.owl.carousel', [500]);
            });
        });
        $('.block-sale__timer').each(function() {
            const timer = $(this);
            const MINUTE = 60;
            const HOUR = MINUTE * 60;
            const DAY = HOUR * 24;

            let left = DAY * 3;

            const format = function(number) {
                let result = number.toFixed();

                if (result.length === 1) {
                    result = '0' + result;
                }

                return result;
            };

            const updateTimer = function() {
                left -= 1;

                if (left < 0) {
                    left = 0;

                    clearInterval(interval);
                }

                const leftDays = Math.floor(left / DAY);
                const leftHours = Math.floor((left - leftDays * DAY) / HOUR);
                const leftMinutes = Math.floor((left - leftDays * DAY - leftHours * HOUR) / MINUTE);
                const leftSeconds = left - leftDays * DAY - leftHours * HOUR - leftMinutes * MINUTE;

                timer.find('.timer__part-value--days').text(format(leftDays));
                timer.find('.timer__part-value--hours').text(format(leftHours));
                timer.find('.timer__part-value--minutes').text(format(leftMinutes));
                timer.find('.timer__part-value--seconds').text(format(leftSeconds));
            };

            const interval = setInterval(updateTimer, 1000);

            updateTimer();
        });
    });

    /*
    // .block-slideshow
    */
    $(function () {
        $('.block-slideshow__carousel').each(function() {
            const owlCarousel = $(this).find('.owl-carousel');

            owlCarousel.owlCarousel({
                items: 1,
                dots: true,
                loop: true,
                rtl: isRTL()
            });
        });
    });

    /*
    // .block-finder
    */
    $(function () {
        $('.block-finder__form-control--select select').on('change', function() {
            const item = $(this).closest('.block-finder__form-control--select');

            if ($(this).val() !== 'none') {
                item.find('~ .block-finder__form-control--select:eq(0) select').prop('disabled', false).val('none');
                item.find('~ .block-finder__form-control--select:gt(0) select').prop('disabled', true).val('none');
            } else {
                item.find('~ .block-finder__form-control--select select').prop('disabled', true).val('none');
            }

            item.find('~ .block-finder__form-control--select select').trigger('change.select2');
        });
    });

    /*
    // .block-header
    */
    (function(){
        // So that breadcrumbs correctly flow around the page title, we need to know its width.
        // This code simply conveys the width of the page title in CSS.

        const media = matchMedia('(min-width: 1200px)');
        const updateTitleWidth = function() {
            const width = $('.block-header__title').outerWidth();
            const titleSafeArea = $('.breadcrumb__title-safe-area').get(0);

            if (titleSafeArea && width) {
                titleSafeArea.style.setProperty('--block-header-title-width', width+'px');
            }
        };

        if (media.matches) {
            updateTitleWidth();
        }

        if (media.addEventListener) {
            media.addEventListener('change', updateTitleWidth);
        } else {
            media.addListener(updateTitleWidth);
        }
    })();

    /*
    // select2
    */
    $(function () {
        $('.form-control-select2, .block-finder__form-control--select select').select2({width: ''});
    });

    /*
    // .vehicle-form
    */
    $(function () {
        $('.vehicle-form__item--select select').on('change', function() {
            const item = $(this).closest('.vehicle-form__item--select');

            if ($(this).val() !== 'none') {
                item.find('~ .vehicle-form__item--select:eq(0) select').prop('disabled', false).val('none');
                item.find('~ .vehicle-form__item--select:gt(0) select').prop('disabled', true).val('none');
            } else {
                item.find('~ .vehicle-form__item--select select').prop('disabled', true).val('none');
            }

            item.find('~ .vehicle-form__item--select select').trigger('change.select2');
        });
    });
})(jQuery);

/*
    // back to top
*/
!function(o){"use strict";o(document).ready(function(){var r=document.querySelector(".rbt-progress-parent path"),n=r.getTotalLength();r.style.transition=r.style.WebkitTransition="none",r.style.strokeDasharray=n+" "+n,r.style.strokeDashoffset=n,r.getBoundingClientRect(),r.style.transition=r.style.WebkitTransition="stroke-dashoffset 10ms linear";function t(){var t=o(window).scrollTop(),e=o(document).height()-o(window).height();r.style.strokeDashoffset=n-t*n/e}t(),o(window).scroll(t);jQuery(window).on("scroll",function(){50<jQuery(this).scrollTop()?jQuery(".rbt-progress-parent").addClass("rbt-backto-top-active"):jQuery(".rbt-progress-parent").removeClass("rbt-backto-top-active")}),jQuery(".rbt-progress-parent").on("click",function(t){return t.preventDefault(),jQuery("html, body").animate({scrollTop:0},1400),!1})})}(jQuery);

/*
    // chat bot
*/
try{
    if(chatbotToggler == null){}
} catch (e) {
    const chatbotToggler = document.querySelector(".chatbot-toggler");
    const closeBtn = document.querySelector(".close-btn");
    const chatbox = document.querySelector(".chatbox");
    const chatInput = document.querySelector(".chat-input textarea");
    const sendChatBtn = document.querySelector(".chat-input span");

    let userMessage = null; // Variable to store user's message
    const API_KEY = "PASTE-YOUR-API-KEY"; // Paste your API key here
    const inputInitHeight = chatInput.scrollHeight;

    const createChatLi = (message, className) => {
        // Create a chat <li> element with passed message and className
        const chatLi = document.createElement("li");
        chatLi.classList.add("chat", `${className}`);
        let chatContent = className === "outgoing" ? `<p></p>` : `<span class="fas fa-robot"></span><p></p>`;
        chatLi.innerHTML = chatContent;
        chatLi.querySelector("p").textContent = message;
        return chatLi; // return chat <li> element
    }

    const generateResponse = (chatElement) => {
        const API_URL = "https://api.together.xyz/inference";
        const messageElement = chatElement.querySelector("p");

        // Define the properties and message for the API request
        const requestOptions = {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer e4a3b9048fe55d6fd6de8f7879ae271b1a0efe408554affef5b566164db58981`
            },
            body: JSON.stringify({
                model: "togethercomputer/llama-2-70b-chat",
                max_tokens: 512,
                prompt: `<s>[INST]\n
Rwayed emerges as a distinguished force in the automotive industry, anchoring its presence in Oujda and redefining the car service experience with unparalleled dedication to quality and customer satisfaction. Operating from the heart of Oujda, Rwayed offers a seamless integration of automotive solutions showcased in their extensive selection of tires and optional repair services.Rwayed's commitment to excellence is further exemplified by its robust service offerings. From tire sales and delivery services across Morocco to optional repair services available exclusively in Oujda, Rwayed ensures that every customer receives the highest level of care and attention.The foundation of Rwayed is deeply rooted in its core values of authenticity and dedication, ensuring that every client's voice is heard and valued. This client-centric approach is supported by a team of highly skilled technicians, including MEZIANI Youssef, BENNANI DOSSE Omar, ABBAOUI Khalil, and HASHAS Jad El Mawla, who are steadfast in their commitment to delivering unparalleled service and expertise. In addition to its impressive range of products and services, Rwayed is committed to maintaining the privacy and security of its clients. The company's Privacy Policy is a testament to its dedication to safeguarding personal information, employing industry-standard security measures, and respecting the confidentiality of user data. Furthermore, the Terms of Service and the comprehensive FAQ section reflect Rwayed's transparency and its endeavor to address client inquiries and concerns promptly and efficiently. With a vision that transcends the ordinary, Rwayed invites customers to explore the myriad of automotive solutions it offers. From tire sales and delivery services to optional repair services, Rwayed stands as a beacon of excellence and innovation in the automotive sector. The journey with Rwayed is not just about obtaining automotive products and services; it's about embarking on a journey defined by quality, trust, and an unwavering commitment to customer satisfaction. Frequently Asked Questions: What shipping methods are available? Standard and express shipping options. Do you ship internationally? Currently within Morocco only. How might I obtain an estimated date of delivery? Confirmation email with tracking information. Can I split my order to ship to different locations? No, single shipping address required. What payments methods are available? Credit/debit cards, bank transfers, cash on delivery. Can I split my payment? Currently not available. How do I return or exchange an item? Within 30 days of delivery, contact customer service. How do I cancel an order? Contact us immediately. How can I track my order? Tracking number provided via email. What should I do if my order arrives damaged? Contact customer service immediately.The project was created on 18 december 2023 \n
act like a costumer support working at CarCrafter and your name is Hamza and ( DON'T greet users only if THEY greets you ) and answer any question from this text and never mention you are reading from a text and if they ask you about money give ranges and in Moroccan DIRHAM , always tell the user to search on the website if user asks for precise price and  don't give additional information answer just what the user ask for direcly without additional introductions\n
        user :\n
        ${userMessage}\n
        Hamza: [/INST]\n`,
                request_type: "language-model-inference",
                temperature: 0.7,
                top_p: 0.7,
                top_k: 50,
                repetition_penalty: 1,
                stop: ["[/INST]", "</s>"],
                negative_prompt: "",
                type: "chat"
            })
        }

        // Send POST request to API, get response and set the reponse as paragraph text
        fetch(API_URL, requestOptions).then(res => res.json()).then(data => {
            messageElement.textContent = data.output.choices[0].text.trim();
        }).catch(() => {
            messageElement.classList.add("error");
            messageElement.textContent = "Oops! Something went wrong. Please try again.";
        }).finally(() => chatbox.scrollTo(0, chatbox.scrollHeight));
    }

    const handleChat = () => {
        userMessage = chatInput.value.trim(); // Get user entered message and remove extra whitespace
        if(!userMessage) return;

        // Clear the input textarea and set its height to default
        chatInput.value = "";
        chatInput.style.height = `${inputInitHeight}px`;

        // Append the user's message to the chatbox
        chatbox.appendChild(createChatLi(userMessage, "outgoing"));
        chatbox.scrollTo(0, chatbox.scrollHeight);

        setTimeout(() => {
            // Display "Thinking..." message while waiting for the response
            const incomingChatLi = createChatLi("Thinking...", "incoming");
            chatbox.appendChild(incomingChatLi);
            chatbox.scrollTo(0, chatbox.scrollHeight);
            generateResponse(incomingChatLi);
        }, 600);
    }

    chatInput.addEventListener("input", () => {
        // Adjust the height of the input textarea based on its content
        chatInput.style.height = `${inputInitHeight}px`;
        chatInput.style.height = `${chatInput.scrollHeight}px`;
    });

    chatInput.addEventListener("keydown", (e) => {
        // If Enter key is pressed without Shift key and the window
        // width is greater than 800px, handle the chat
        if(e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
            e.preventDefault();
            handleChat();
        }
    });

    sendChatBtn.addEventListener("click", handleChat);
    closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
    chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
}


document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('.alert-success, .alert-danger');
        flashMessages.forEach(flashMessage => {
            flashMessage.classList.add('fade-out');

            flashMessage.addEventListener('animationend', () => {
                flashMessage.style.display = 'none'; // Masque complètement l'élément après l'animation.
            });
        });
    }, 8000); // Les alertes commencent à disparaître après 5 secondes.
});


document.addEventListener('DOMContentLoaded', function() {
    var showModal = document.body.getAttribute('data-show-modal') === 'true';
    if (showModal) {
        $('#loginMessageModal').modal('show');
    }

    // Optionnel : Nettoyer la session après l'affichage du modal
    if (showModal) {
        fetch('/nettoyer-modal'); // Assurez-vous que cette route nettoie `show_login_modal` de la session
    }
});

$(document).ready(function() {
    $('#newsletter-form').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            success: function(response) {
                if(response.subscribed) {
                    $('#subscription-message').removeClass('alert-danger').removeClass('alert-success').addClass('alert-warning').text("Already subscribed").fadeIn().delay(5000).fadeOut();
                } else {
                    if (typeof response.errors === 'undefined') {
                        $('#subscription-message').removeClass('alert-danger').removeClass('alert-warning').addClass('alert-success').text("Successfully subscribed").fadeIn().delay(5000).fadeOut();
                    } else {
                        $('#subscription-message').removeClass('alert-success').removeClass('alert-warning').addClass('alert-danger').text(response.errors.join(', ')).fadeIn().delay(5000).fadeOut();
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#subscription-message').removeClass('alert-success').removeClass('alert-warning').addClass('alert-danger').text("Ops! Server error").fadeIn().delay(5000).fadeOut();
            }
        });
    });
});