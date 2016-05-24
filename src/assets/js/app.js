
(function( $ ) {

    $.fn.initPatternViewport = function() {
        this.filter('iframe').each(function(){

            var vars = {};
            vars.patternViewport = this;
            vars.$patternViewport = $(this);
            vars.$window = $(window);
            vars.$body = $('body');
            vars.isInit = false;

            vars.hasViewportReference = false;

            if ($('[data-reference="' + vars.$patternViewport.attr('id')+ '"]').length) {
                vars.hasViewportReference = true;
                vars.$patternViewportReference = $('[data-reference="' + vars.$patternViewport.attr('id')+ '"]');
            }
            //vars.


            vars.functions = {};

            vars.functions.getViewportDocument = function() {
                return vars.patternViewport.document || vars.patternViewport.contentDocument || vars.patternViewport.contentWindow.document;
            };

            vars.functions.initViewportReference = function() {
                if (vars.hasViewportReference) {
                    vars.$patternViewportReference.html('<span class="pattern-viewport-reference-width">Viewport Breite: ' + vars.$patternViewport.width() + 'px</span>');
                }
            };

            vars.functions.handlePattern = function(item) {
                var itemInfoHandler = $('<a href="#" class="pattern-info-link">{}</a>');

                itemInfoHandler.on('click', function(event) {
                    event.preventDefault();
                    $.ajax({
                        url: item.data('source'),
                        success: function(data) {
                            data = data.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                            vars.infoContainer.show('<pre>' + data + '</pre>');
                        }
                    });
                });

                var itemHeadline = $('.pattern-headline', item);
                if (itemHeadline.length) {
                    itemInfoHandler.appendTo(itemHeadline);
                }

            };

            vars.infoContainer = {
                isInit: false,
                init: function() {
                    vars.infoContainer.$element = $('<div class="info-container"></div>');
                    vars.infoContainer.$content = $('<div class="info-container_content"></div>');
                    vars.infoContainer.$content.appendTo(vars.infoContainer.$element);

                    vars.infoContainer.$closer = $('<a href="#">Schließen</a>');
                    vars.infoContainer.$closer.on('click', function(event) {
                        event.preventDefault();
                        vars.infoContainer.hide();
                    });

                    vars.infoContainer.$closer.appendTo(vars.infoContainer.$element);

                    vars.infoContainer.$element.appendTo(vars.$body);
                    vars.infoContainer.isInit = true;
                },
                show: function(content) {
                    if (!vars.infoContainer.isInit) {
                        vars.infoContainer.init();
                    }
                    vars.infoContainer.$content.html(content);
                    vars.infoContainer.$element.addClass('js-active');
                },
                hide: function() {
                    if (vars.infoContainer.isInit) {
                        vars.infoContainer.$element.removeClass('js-active');
                        vars.infoContainer.$content.html('');
                    }
                }
            };

            vars.patternViewportDocument = vars.functions.getViewportDocument();

            jQuery(vars.$patternViewport).load(function(){
                vars.pattern = vars.$patternViewport.contents().find('[data-source]');
                vars.pattern.each(function(index, el) {
                    vars.functions.handlePattern($(el));
                });
                vars.isInit = true;
            });

            window.setTimeout(function() {
                if (!vars.isInit) {
                    vars.pattern = vars.$patternViewport.contents().find('[data-source]');
                    vars.pattern.each(function(index, el) {
                        vars.functions.handlePattern($(el));
                    });
                    vars.isInit = true;
                }
            }, 1000);

            jQuery(vars.$window).on('resize', function(){
                vars.functions.initViewportReference();
            });

            vars.functions.initViewportReference();

            return this;
        });
    };

}( jQuery ));


jQuery(document).ready(function() {

    jQuery('#pattern-viewport').initPatternViewport();
});