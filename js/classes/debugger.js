import $ from "jquery";
import {fetchHelper, setFetchDebugger} from "../helpers/fetch";
import Pachno from "./pachno";

class Debugger {
    constructor(debug_url) {
        this.debug_url = debug_url;
        this.calls = [];

        setFetchDebugger(this);
    }

    loadDebugInfo(debug_id, cb) {
        let url = this.debug_url.replace('___debugid___', debug_id);
        Pachno.fetch(url, {
            method: 'GET',
            loading: {indicator: '#___PACHNO_DEBUG_INFO___indicator'},
            success: {update: '#___PACHNO_DEBUG_INFO___'},
            complete: {
                callback: cb,
                show: '#___PACHNO_DEBUG_INFO___'
            },
            debug: false
        });
    }

    updateDebugInfo (information) {
        this.calls.push(information);

        let $logAjaxItems = $('#log_ajax_items');
        if ($logAjaxItems) {
            $('#log_ajax_items').html('');
            if ($('#debug_ajax_count')) {
                $('#debug_ajax_count').html(this.calls.length);
            }

            let ct = function (time) {
                return (time < 10) ? '0' + time : time;
            };

            for(let info of this.calls) {
                let content = '<li><span class="badge timestamp">' + ct(info.time.getHours()) + ':' + ct(info.time.getMinutes()) + ':' + ct(info.time.getSeconds()) + '.' + ct(info.time.getMilliseconds()) + '</span><span class="badge timing"><i class="far fa-clock"></i>' + info.loadtime + '</span><span class="badge timing session" title="Time spent by php loading session data"><i class="far fa-hdd"></i>' + info.session_loadtime + '</span><span class="badge timing calculated" title="Calculated load time, excluding session load time"><i class="fa fa-calculator"></i>' + info.calculated_loadtime + '</span><span class="partial">' + info.location + '</span> <a class="button" style="float: right;" href="javascript:void(0);" onclick="Pachno.loadDebugInfo(\'' + info.debug_id + '\');">Debug</a></li>';
                $logAjaxItems.prepend(content, 'top');
            }
        }
    }
}

export default Debugger;
