/**
 * jQuery Select2 Multi checkboxes
 * - allow to select multi values via normal dropdown control
 * 
 * author      : wasikuss
 * repo        : https://github.com/wasikuss/select2-multi-checkboxes
 * inspired by : https://github.com/select2/select2/issues/411
 * License     : MIT
 */
(function($) {
  var S2MultiCheckboxes = function(options, element) {
    var self = this;
    self.options = options;
    self.$element = $(element);
	var values = self.$element.val();
    self.$element.removeAttr('multiple');
    self.select2 = self.$element.select2({
      allowClear: true,
      //minimumResultsForSearch: -1,
      placeholder: options.placeholder,
      closeOnSelect: false,
      templateSelection: function() {
        return self.options.templateSelection(self.$element.val() || [], $('option', self.$element).length);
      },
      templateResult: function(result) {
        if (result.loading !== undefined)
          return result.text;
        return $('<div>').text(result.text).addClass(self.options.wrapClass);
      }
    }).data('select2');
    self.select2.$results.off("mouseup").on("mouseup", ".select2-results__option[aria-selected]", (function(self) {
      return function(evt) {
        var $this = $(this);

        var data = $this.data('data');

        if ($this.attr('aria-selected') === 'true') {
          self.trigger('unselect', {
            originalEvent: evt,
            data: data
          });
          return;
        }

        self.trigger('select', {
          originalEvent: evt,
          data: data
        });
      }
    })(self.select2));
    self.$element.attr('multiple', 'multiple').val(values).trigger('change.select2');
  }

  $.fn.extend({
    select2MultiCheckboxes: function() {
      var options = $.extend({
        placeholder: 'Choose elements',
        templateSelection: function(selected, total) {
          return selected.length + ' > ' + total + ' total';
        },
        wrapClass: 'wrap'
      }, arguments[0]);

      this.each(function() {
        new S2MultiCheckboxes(options, this);
      });
    }
  });
})(jQuery);


// Init script
jQuery(function($) {
  $('.select2-multiple').select2MultiCheckboxes({
    placeholder: "Select From List",
    width: "auto",
    templateSelection: function(selected, total) {
      return "Selected " + selected.length + " of " + total;
    }
  })
});