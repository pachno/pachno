<script type="text/javascript">
    var Pachno;

    require(['domReady', 'pachno/index'], function (domReady, pachno_index_js) {
        domReady(function () {
            Pachno = pachno_index_js;
                $$('.dashboard_add_view_container').each(function (davc) {
                    davc.on('click', Pachno.Main.Dashboard.addViewPopup);
                });
            });
        });
</script>