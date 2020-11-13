$(document).ready(function () {

    $('#btnAnadir').click(function () {
        $('#tblJefesdelinea tr').removeClass();
        if ($('#opcJefes').val() != '') {
            if ($('#idsjefe' + $('#opcJefes').val()).data('id') == undefined) {
                var html = '<tr id="idsjefe' + $('#opcJefes').val() + '" data-id="1">' + 
                                '<td><center>' + $('#opcJefes option:selected').text() + '</center></td>' +
                                '<td><center><input type="checkbox" class="chkJefe" data-id="' + $('#opcJefes').val() + '"></center></td>' +
                           '</tr>';
                $('#tblJefesdelinea').append(html);
            }
            $('.chkJefe[data-id="' + $('#opcJefes').val() + '"]').parents('tr').addClass('active-row');
        } else {
            $('#opcJefes').focus();
        }
    });

    $('#tblJefesdelinea').on('click', '.chkJefe', function () {
        var idactor = $(this).data('id');
        $.ajax({
            type: 'post',
            url: '/mantenimiento/jefelinea_gestor/',
            data:{'idactor': idactor},
            success: function () {}
        });
    });

});