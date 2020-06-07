const initialize = function (options) {
    // if (window.location.hash) {
    //     Pachno.Project.Planning.Whiteboard.checkNav();
    // } else {
    //     Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus();
    //     Pachno.Project.Planning.Whiteboard.retrieveWhiteboard();
    // }
    //
    // $('#planning_whiteboard_columns_form_row').sortable({
    //     handle: '.draggable',
    //     axis: 'x',
    //     update: Pachno.Project.Planning.Whiteboard.setSortOrder
    // });

    $('#planning_indicator').hide();
    $('#planning_filter_title_input').prop('disabled', false);
};

const setupListeners = function() {
    $('body').on('click', '#selected_milestone_input li', Pachno.Project.Planning.Whiteboard.retrieveMilestoneStatus);
    $(window).on('hashchange', Pachno.Project.Planning.Whiteboard.checkNav);
};

export default initialize;