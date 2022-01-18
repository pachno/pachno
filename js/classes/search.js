class Search {
    constructor(options) {
        this.save_columns_url = options.save_columns_url;
        this.history_url = options.history_url;
        this.dynamic_callback_url = options.dynamic_callback_url;
        this.project_id = options.project_id;
        this.is_saved = false;
        this.results_loaded = false;
        this.sort_field = undefined;
        this.sort_direction = undefined;
        this.offset = 0;
        this.results_per_page = undefined;
        this.is_dirty = false;
        this.current_result_view = undefined;
        this.result_views = {};

        this.setupListeners()
            .then(() => {
                if (options.show_results) {
                    this.updateResults(true);
                } else {
                    this.updateSavedSearchCounts();
                }
            });
    }

    toggleColumn(column) {
        $('.sc_' + column).toggleClass('hidden');
    };

    updateColumnVisibility(event) {
        const $input = $(event.target)
        this.toggleColumn($input.val());
        this.saveColumnVisibility(true);
    }

    saveColumnVisibility(force) {
        if (!this.is_saved || force) {
            Pachno.fetch(this.save_columns_url, {
                form: 'find_issues_form',
                method: 'POST'
            });
        }
    };

    setGrouping(event) {
        event.stopPropagation();
        const $input = $(event.target)
        // Pachno.Search.setFilterSelectionGroupSelections(this);
        // Pachno.Search.setFilterValue(element, true);

        if ($input.data('groupby') == '') {
            $('#filter_grouping_options .grouporder').addClass('hidden');
        } else {
            $('#filter_grouping_options .grouporder').removeClass('hidden');
        }

        this.updateResults();
    };

    updateSavedSearchCounts() {
        let search_ids = '';
        const searchitems = $('.savedsearch-item');

        searchitems.each(function (searchitem) {
            search_ids += '&search_ids[]='+$(searchitem).data('search-id');
        });

        Pachno.fetch(Pachno.data_url, {
            method: 'GET',
            params: `&say=getsearchcounts&project_id=${this.project_id}${search_ids}`,
            success: {
                callback: function (json) {
                    searchitems.each(function (searchitem) {
                        const $badge = $(searchitem).down('.count-badge');
                        if ($badge !== undefined) {
                            $badge.html(json[$(searchitem).data('search-id')]);
                        }
                    });
                }
            }
        });
    };

    loadDynamicChoices() {
        let filters_containers = [];
        let parameters = '&project_id=' + $('#filter_project_id_value_input').val();
        const $filter_subprojects_value_input = $('#filter_subprojects_value_input');
        if ($filter_subprojects_value_input) {
            parameters += '&subprojects=' + $filter_subprojects_value_input.val();
        }

        ['build', 'component', 'edition', 'milestone'].each(function (elm) {
            const $filter_element = $('#interactive_filter_' + elm);
            const $results_container = $($filter_element.find('.interactive_menu_values'));
            $results_container.find('input[type=checkbox]').each(function () {
                if (this.is(':checked')) {
                    parameters += '&existing_ids[' + $filter_element.data('filterkey') + '][' + this.val() + ']=' + this.val();
                }
            });
            filters_containers.push({filter: $filter_element, container: $results_container});
        });
        Pachno.fetch(this.dynamic_callback_url, {
            params: parameters,
            loading: {
                callback: function () {
                    filters_containers.each(function (details) {
                        details['container'].addClass('updating');
                    });
                }
            },
            success: {
                callback: function (json) {
                    filters_containers.each(function (details) {
                        details['container'].html(json.results[details['filter'].data('filterkey')]);
                        // window.setTimeout(function () {
                        //     var si = details['filter'].down('input[type=search]');
                        //     if (si != undefined) {
                        //         si.data('previous-value') = '';
                        //         Pachno.Search.filterFilterOptionsElement(si);
                        //     }
                        // }, 250);
                        details['container'].removeClass('updating');
                    });
                }
            }
        });
    }

    checkToggledCheckboxes() {
        const num_checked = $('#search-results input[type=checkbox]:checked').length;

        if (num_checked == 0) {
            $('#search-bulk-actions').addClass('unavailable');
            $('#bulk_action_submit').addClass('disabled');
        } else {
            $('#search-bulk-actions').removeClass('unavailable');
            const selected_radio_value = $('#search-bulk-action-form input[name=search_bulk_action]:checked', '#search-bulk-action-form').val();
            if (selected_radio_value) {
                $('#bulk_action_submit').removeClass('disabled');
            }
        }
    }

    toggleCheckboxes(event) {
        const $input = $(event.target);
        let do_check = true;

        if ($input.hasClass('semi-checked')) {
            $input.removeClass('semi-checked');
            $input.prop('checked', true);
            do_check = true;
        } else {
            do_check = $input.is(':checked');
        }

        $($input.parents('.results_container')).find('.results_body input[type=checkbox]').each(function () {
            $(this).prop('checked', do_check);
        });

        this.checkToggledCheckboxes();
    }

    toggleCheckbox(event) {
        const $input = $(event.target);
        const num_unchecked = $($input.parents('.results_container')).find('input[type=checkbox]:not(:checked)').length;
        const num_checked = $($input.parents('.results_container')).find('input[type=checkbox]:checked').length;

        const $header_checkbox = $($input.parents('.results_body')).find('.row.header input[type=checkbox]');
        if (num_unchecked == 0) {
            $header_checkbox.prop('checked', true);
            $header_checkbox.removeClass('semi-checked');
        } else if (num_checked > 0) {
            $header_checkbox.prop('checked', true);
            $header_checkbox.addClass('semi-checked');
        } else {
            $header_checkbox.prop('checked', false);
            $header_checkbox.removeClass('semi-checked');
        }

        this.checkToggledCheckboxes();
    }

    setColumns(template, available_columns, visible_columns, default_columns, template_parameter, filters) {
        this.current_result_view = template.name;
        this.result_views[template.name] = {
            available_columns: available_columns,
            visible_columns: visible_columns,
            default_visible_columns: default_columns
        };
        for (const key in this.result_views[template.name].available_columns) {
            if (!this.result_views[template.name].available_columns.hasOwnProperty(key))
                continue;

            const column = this.result_views[template.name].available_columns[key];
            if (this.result_views[template.name].visible_columns.indexOf(column) != -1) {
                $('#search_column_' + column + '_toggler_checkbox').prop('checked', true);
            } else {
                $('#search_column_' + column + '_toggler_checkbox').prop('checked', false);
            }
        }
        $(`#filter_selected_template_${template.name}`).prop('checked', true);
        if (template.parameter !== '') {
            $('#search_template_parameter_container_header').html(template.parameter_header);
            $('#search_filter_parameter_input').attr('placeholder', template.parameter_text);
            $('#search_filter_parameter_input').val(template_parameter);
            $('#search_template_parameter_container').removeClass('hidden');
        } else {
            $('#search_template_parameter_container').addClass('hidden');
        }
        for (const filter of filters) {
            const $filter_element = $('#additional_filter_' + filter + '_link');
            $filter_element.addClass('disabled');
        }
    }

    updateResults(force) {
        const $find_issues_form = $('#find_issues_form');
        const url = $find_issues_form.prop('action');
        const parameters = $find_issues_form.serialize();
        const search = this;

        if (force === true || this.results_loaded) {
            $('nav.sidebar').addClass('collapsed');
            Pachno.fetch(url, {
                form: 'find_issues_form',
                method: 'POST',
                loading: {
                    indicator: '#search_results_loading_indicator',
                    callback: function () {
                        if (history.pushState) {
                            history.pushState({caller: 'liveUpdate'}, '', search.history_url + '?' + parameters);
                        }
                    }
                },
                success: {update: '#search-results'}
            }).then((json) => {
                for (const issue_json of json.issues) {
                    Pachno.addIssue(issue_json);
                }
                if (!search.results_loaded) {
                    search.updateSavedSearchCounts();
                }
                search.setColumns(json.template, json.available_columns, json.visible_columns, json.default_columns, json.template_parameter, json.applied_filters);
                $('#findissues_num_results_span').html(json.num_issues);
                // if (! $('#findissues_search_title').visible() && ! $('#findissues_search_generictitle').visible()) {
                //     $('#findissues_search_generictitle').show();
                // }
                $('#findissues_num_results').show();
                $('#interactive_save_button').show();
                search.results_loaded = true;
                search.is_saved = false;
                if (search.is_dirty) {
                    search.loadDynamicChoices();
                    search.is_dirty = false;
                }
            });
        }
    }

    updateOffset(offset) {
        $('#search_offset_input').val(offset);
        this.updateResults(true);
    }

    sortResults(event) {
        const $input = $(event.target);
        if ($input.data('sort-field') !== undefined) {
            const direction = ($input.data('sort-direction') == 'asc') ? 'desc' : 'asc';
            $('#search_sortfields_input').val($input.data('sort-field') + '=' + direction);
            this.updateResults(true);
        }
    }

    pickTemplate(event, element) {
        event.stopPropagation();
        var is_selected = this.hasClass('selected');
        var current_elm = this;
        if (!is_selected) {
            $('.template-picker').each(function (element) {
                if (element == current_elm) {
                    current_elm.addClass('selected');
                    $('#filter_selected_template').val(current_elm.dataset.templateName);
                    if (current_elm.dataset.grouping == '1') {
                        $('#search_grouping_container').removeClass('nogrouping');
                        $('#search_grouping_container').removeClass('parameter');
                        $('#search_filter_parameter_input').prop('disabled', true);
                    } else {
                        $('#search_grouping_container').addClass('nogrouping');
                        if (current_elm.dataset.parameter == '1') {
                            $('#search_grouping_container').addClass('parameter');
                            $('#search_filter_parameter_description').html(current_elm.dataset.parameterText)
                            $('#search_filter_parameter_input').prop('disabled', false);
                        } else {
                            $('#search_grouping_container').removeClass('parameter');
                        }
                    }
                } else {
                    element.removeClass('selected');
                }
            });
        }
        $('.filter,.interactive_plus_button').each(function (element) {
            if (element != this)
                element.removeClass('selected');
        });
        if (is_selected)
            this.removeClass('selected');
        else
            this.addClass('selected');

        Pachno.Search.liveUpdate();
    };

    bulkContainerChanger() {
        const selected_radio_value = $('input[name=search_bulk_action]:checked').val(),
            sub_container_id = '#bulk_action_subcontainer_' + selected_radio_value;

        $('.bulk_action_subcontainer').addClass('hidden');
        if ($(sub_container_id)) {
            $(sub_container_id).removeClass('hidden');
            $('#bulk_action_submit').removeClass('disabled');
            const $dropdown_element = $(sub_container_id).find('.focusable');
            if ($dropdown_element != undefined)
                $dropdown_element.focus();
        } else {
            $('#bulk_action_submit').addClass('disabled');
        }
    };

    setupListeners() {
        return new Promise((resolve, reject) => {
            const search = this;
            const $body = $('body');
            const $find_issues_form = $('#find_issues_form');

            $find_issues_form.trigger("reset");
            $body.on('click', '.search_column_toggler', function (event) {
                search.updateColumnVisibility(event);
            });
            $body.on('click', '#search_grouping_container li', function (event) {
                search.updateColumnVisibility(event);
            });
            $body.on('click', '#search-results .row.header .header:not(.nosort)', function (event) {
                search.sortResults(event);
            });
            $body.on('click', '.trigger-update-search-page', function (event) {
                search.updateOffset($(this).data('offset'));
            });
    //     $('.template-picker').each(function (element) {
    //         element.on('click', Pachno.Search.pickTemplate);
    //     });
    //
            $body.on('click', '.sca_actions input[type="checkbox"]', function (event) {
                search.toggleCheckbox(event);
            });
            // issue checkboxes select all
            $body.on('click', '.sca_action_selector input[type="checkbox"]', function (event) {
                search.toggleCheckboxes(event);
            });

            $body.on('click', 'input[type=radio].bulk-action-checkbox', function (event) {
                search.bulkContainerChanger();
            });

            $body.on('click', '.search-trigger-reload', function (event) {
                search.updateResults(true);
            });

            $body.on('click', '#search-filters .remove-button', function () {
                const $filter = $(this).parents('.filter');
                const filter = $filter.data('filterkey');

                const do_update = ($(`#filter_${filter}_value_input`).val() != '');
                $('#additional_filter_' + filter + '_link').removeClass('disabled');
                $('#search-filters-hidden-container').append($filter.remove());

                if (do_update) {
                    search.updateResults(true);
                }
            });

            $body.on('click', '.trigger-add-filter:not(.disabled)', function (event) {
                const $filter_link = $(this);
                $filter_link.addClass('disabled');
                const filter = $filter_link.data('filter');
                const $filter_element = $('#interactive_filter_' + filter);
                $('#search-filters').append($filter_element.remove());
                setTimeout(function () {
                    $('#interactive_filter_' + filter).find('.fancy-dropdown').addClass('active');
                }, 150);
            });

            $body.on('click', '.filter .fancy-dropdown input[type=checkbox],.filter .fancy-dropdown input[type=radio]', function () {
                // var filter = $(this);
                // if ($('.filter_' + filter.data('filterkey'), filter).length) {
                //     $('.filter_' + filter.data('filterkey'), filter).data('dirty', 'dirty');
                // }
                // else {
                //     $('#filter_' + filter.data('filterkey')).data('dirty', 'dirty');
                // }
                search.updateResults(true);
            });
    //
    //     Pachno.Search.initializeIssuesPerPageSlider();
    //
    //     var sff = $('#search-filters');
    //     $('#add-search-filter-button').find('.list-item').each(function (element) {
    //         element.on('click', Pachno.Search.addFilter);
    //         if (sff.down('#interactive_filter_' + element.dataset.filter)) {
    //             element.addClass('disabled');
    //         }
    //     });
    //     var ifts = $('.filter_searchfield');
    //     Pachno.ift_observers = {};
    //     ifts.each(function (ift) {
    //         ift.data('last-value', '');
    //         ift.on('keyup', function (event, element) {
    //             if (Pachno.ift_observers[ift.id])
    //                 clearTimeout(Pachno.ift_observers[ift.id]);
    //             if ((ift.val().length >= 3 || ift.val().length == 0 || (ift.dataset.maxlength && ift.val().length > parseInt(ift.dataset.maxlength))) && ift.val() != ift.data('last-value')) {
    //                 Pachno.ift_observers[ift.id] = setTimeout(function () {
    //                     Pachno.Search.liveUpdate(true);
    //                     ift.data('last-value', ift.val());
    //                     var flt = ift.parents('.filter');
    //                     if (flt !== undefined) {
    //                         Pachno.Search.updateFilterVisibleValue(flt, ift.val());
    //                     }
    //                 }, 1000);
    //             }
    //         });
    //
    //     });
            resolve();
        });

    }
}

export default Search;
window.Search = Search;
