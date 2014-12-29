'use strict';

/* globals: $, bootbox */

var Utils = {
    ModalEdit: function(route) {
        $.get(route, {}, function(data){
            bootbox.dialog({
                'title': 'Edit',
                'message': data,
                'buttons': {
                    'Save': {
                        label: '<i class="fa fa-floppy-o"></i> Save',
                        className: 'btn-success',
                        callback: function(){
                            var $form = $('form.modal-form');

                            $form.submit();
                            return false;
                        }
                    }
                }
            })
        });
    }
};
console.log("hello");