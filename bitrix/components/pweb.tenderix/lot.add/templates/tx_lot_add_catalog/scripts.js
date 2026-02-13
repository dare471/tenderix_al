$(function () {
    arrItem();
    $("#addItem").click(function () {
        arrItem();
    });
    $("#supplier-count").html(" (" + $("select[id=select-private-supplier] option").size() + ")");

    $("#supplier-view").colorbox({
        inline: true,
        href: "#supplier-block",
        opacity: 0.5,
        width: "900px",
        maxHheight: "80%",
        top: "10%",
        onClosed: function () {
            $("#select-private-supplier option").each(function () {
                this.selected = true;
            });
            $("#supplier-count").html(" (" + $("select[id=select-private-supplier] option").size() + ")");
        },
        onOpen: function () {
            $('#select-private-supplier option:selected').each(function () {
                this.selected = false;
            });
        }
    });
	$('#button-select-add-supplier').click(function() {
		var select_val = '';
		var select_html = '';
		$('#select-all-supplier').find('option').each(function() {
			select_val = $(this).val();
			select_html = $(this).html();
			var select_true = false;
			if ($(this).prop('selected') ) {
				$('#select-private-supplier').find('option').each(function () {
					if ($(this).val() == select_val) select_true = true;
				});
				if (!select_true) {
					$('#select-private-supplier').append('<option value="' + select_val + '">' + select_html + '</option>');
				}
			}
		});
	});
	$("#button-select-del-supplier").click(function () {
		$('#select-private-supplier option:selected').each(function () {
			$(this).remove();
		});
	});

    $("#private").click(function () {
        var supplier_cnt = $("select[id=select-all-supplier] option").size();
        if ($(this).is(":checked") && supplier_cnt <= 0) {
            $.ajax({
                url: "<?= $templateFolder ?>/ajax.php",
                type: "POST",
                data: "action=getSupplier",
                dataType: "json",
                beforeSend: function () {
                    $("#supplier-view-load").show();
                },
                success: function (data) {
                    for (var i = 0; i < data.length; i++) {
                        $("#select-all-supplier").append('<option value="' + data[i].id + '">' + data[i].company + '</option>');
                    }
                    $("#supplier-view-load").hide();
                    $("#supplier-view").show();
                }
            });
        }
        if ($(this).is(":checked") && supplier_cnt > 0) {
            $("#supplier-view").show();
        } else {
            $("#supplier-view").hide();
        }
    });


    $('#searchSupplier').keyup(function () {

        var n = "0";

        if ($('#searchSupplier').val().length >= 0) {
            $('#select-all-supplier').empty();
            var search22 = $('#searchSupplier').val();
            var search44 = Number(search22);

            var status = $('#searchStatus').val();

            var props = "";
            <?
            $svvProp = array();
            $rsvProp = CTenderixUserSupplierProperty::GetList($by = "", $order = "", array());
            while ($svProp = $rsvProp->Fetch()) { ?>
            var prop<?=$svProp["ID"];?> = $('#searchProp<?=$svProp["ID"];?>').val();
            //props .= "&prop<?=$svProp["ID"];?>= "+$('#searchProp<?=$svProp["ID"];?>').val();
            <?
            }
            ?>

            if ((search44 != search22) && ($('#searchSupplier').val().length >= 3)) {
                cifr22 = '0';
            }
            else {
                cifr22 = '1';
            }


            $.ajax({
                url: "<?= $templateFolder ?>/ajax.php",
                type: "POST",
                data: "action=getSupplier&nameCompany=" + search22 + "&status=" + status,
                dataType: "json",
                success: function (data) {
                    window.n = '0';
                    window.n = data.length;
                    //alert(window.n);
                    $("#supplier-view-load").hide();
                    $("#supplier-view").show();
                    if (window.n == 0) {
                        $("span.results").empty();
                        $("span.results").fadeIn().append("Ничего не найдено");
                    } else {
                        $("span.results").empty();
                        $("span.results").fadeIn().append("<b>Результатов:</b> " + window.n + ".");
                    }
                    $('#select-all-supplier').empty();
                    for (var i = 0; i < data.length; i++) {
                        $("#select-all-supplier").append('<option value="' + data[i].id + '" selected>' + data[i].company + '</option>');
                    }
                }
            });

            return false;
        }
        if ($('#searchSupplier').val().length == 0) {
            $("span.results").empty();
        }

    });
    
});

function arrItem() {
    var numProp = $("#numProp").val();
    var newProp = $("#newProp").val();
    $.ajax({
        url: "<?= $templateFolder ?>/ajax.php",
        type: "POST",
        data: "action=addItem&numProp=" + numProp + "&newProp=" + newProp,
        beforeSend: function () {
            $("#addItem").attr("disabled", true);
        },
        success: function (data) {
            $("#numProp").val(parseInt(numProp) + 1);
            $("#newProp").val(parseInt(newProp) + 1);
            $("#table_spec").append(data);
            $("#addItem").attr("disabled", false);
        }
    });
}