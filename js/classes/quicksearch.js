const $body = $('body');
import UI from "../helpers/ui";
import Pachno from "./pachno";
import $ from "jquery";

class Quicksearch {
    constructor(url) {
        this.url = url;
        this.enabled = false;
        this.setupListeners();
        this.highlighted_choice = undefined;
        this.selected_choice = undefined;
        this.default_choices = [];
        this.choices = [];
        this.visible_choices = [];
        this.$results_container = $('#quicksearch-results');
        this.$input = $('#quicksearch-input');
    }

    show(default_value, choices) {
        this.enabled = true;
        this.highlighted_choice = undefined;
        this.selected_choice = undefined;
        $('#current-command-description').html('');

        if (this.$input.val() === "" || this.visible_choices.length === 0) {
            this.visible_choices = this.default_choices;
        } else if (choices !== undefined) {
            this.visible_choices = choices;
        }

        this.showChoices();

        if (default_value !== undefined) {
            this.$input.val(default_value);
            this.updateHighlightedChoiceFromInput();
        } else {
            this.$input.val('');
        }

        $('#quicksearch-container').addClass('active');
        setTimeout(() => {
            this.$input.focus();
        }, 500);
    }

    hide() {
        this.enabled = false;
        this.$input.blur();
        this.$input.val('');
        $('#quicksearch-container').removeClass('active');
    }

    showChoices() {
        this.$results_container.html('');

        for (const choice of this.visible_choices) {
            console.log(choice);
            if (choice.previous_choice === undefined) {
                console.error(choice);
            }
            const choice_description = (choice.description !== undefined) ? `<span class="description">${choice.description}</span>` : '';
            const choice_icon = (choice.icon !== undefined) ? `<span class="icon">${UI.fa_image_tag(choice.icon.name, {}, choice.icon.type)}</span>` : '';
            const html = `
            <div class="result-item">
                ${choice_icon}
                <span class="name">
                    <span class="title"><span class="count-badge">${choice.shortcut}</span><span>${choice.name}</span></span>
                    ${choice_description}
                </span>
            </div>
            `;

            this.$results_container.append(html);
        }
    }

    updateSelectedChoice(remove) {
        if (this.selected_choice === undefined) {
            $('#current-command-description').html('');
            this.$input.data('shortcut', '');
            return;
        }

        const do_replace = this.$input.data('shortcut') !== this.selected_choice.shortcut;

        $('#current-command-description').html(this.selected_choice.description);

        if (remove === undefined) {
            // let value = this.$input.val().trim();
            // if (do_replace && value.startsWith(this.$input.data('shortcut')) && !this.selected_choice.shortcut.startsWith(this.$input.data('shortcut'))) {
            //     value = value.substr(this.$input.data('shortcut').length);
            // }
            // if (value === this.selected_choice.shortcut) {
                this.$input.val(`${this.selected_choice.shortcut} `);
            // } else {
            //     this.$input.val(`${this.selected_choice.shortcut} ${value}`);
            // }
        }
        this.$input.data('shortcut', this.selected_choice.shortcut);

        if (do_replace) {
            if (this.selected_choice.choices !== undefined) {
                this.visible_choices = this.selected_choice.choices;
                for (const index in this.visible_choices) {
                    if (this.visible_choices.hasOwnProperty(index)) {
                        this.visible_choices[index].previous_choice = this.selected_choice;
                    }
                }
                this.showChoices();
            } else if (this.selected_choice.type == TYPES.dynamic_choices) {
                this.updateDynamicChoices(this.selected_choice.event, this.selected_choice.event_value);
            } else if (this.selected_choice.type == TYPES.backdrop) {
                UI.Backdrop.show(this.selected_choice.backdrop_url);
                this.hide();
            } else {
                this.showChoices();
                this.selectHighlightedChoice();
            }
        }
    }

    updateDynamicChoices(event, event_value) {
        this.$results_container.html(UI.fa_image_tag('spinner', { classes: ['fa-spin', 'indicator'] }));
        Pachno.trigger(event, event_value);
    }

    navigateNextChoice() {
        if (this.highlighted_choice === undefined || this.highlighted_choice == this.visible_choices.length - 1) {
            this.highlighted_choice = 0;
        } else {
            this.highlighted_choice += 1;
        }
        this.updateHighlightedChoice();
    }

    navigatePreviousChoice() {
        if (this.highlighted_choice === undefined) {
            this.highlighted_choice = this.visible_choices.length - 1;
        } else if (this.highlighted_choice == 0) {
            this.highlighted_choice = undefined;
        } else {
            this.highlighted_choice -= 1;
        }
        this.updateHighlightedChoice();
    }

    updateHighlightedChoice() {
        const $children = this.$results_container.children();
        const quicksearch = this;

        if (quicksearch.highlighted_choice === undefined) {
            this.$input.focus();
        }

        $children.each(function (index) {
            if (index === quicksearch.highlighted_choice) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
    }

    updateHighlightedChoiceFromInput(remove) {
        const value = this.$input.val().trim();
        let found = false;
        if (!value.length)
            return;

        for (const index in this.visible_choices) {
            if (!this.visible_choices.hasOwnProperty(index))
                return;

            const choice = this.visible_choices[index];

            if (value.startsWith(choice.shortcut)) {
                this.highlighted_choice = parseInt(index);
                found = true;
                break;
            }
        }

        if (!found && remove) {
            for (const choice of this.visible_choices) {
                if (choice.previous_choice !== undefined && value.startsWith(choice.previous_choice.shortcut)) {
                    this.selected_choice = choice.previous_choice;
                    this.highlighted_choice = undefined;
                    found = true;
                    break;
                }
            }
        }

        if (!found) {
            this.highlighted_choice = undefined;
            this.selected_choice = (this.selected_choice !== undefined) ? this.selected_choice.previous_choice : undefined;
            if (remove) {
                if (this.selected_choice !== undefined && this.selected_choice.choices) {
                    this.visible_choices = this.selected_choice.choices;
                    this.showChoices();
                } else if (this.selected_choice === undefined) {
                    this.visible_choices = this.default_choices;
                    this.showChoices();
                }
                this.updateSelectedChoice(remove);
            }
        }

        if ((this.selected_choice === undefined || found) && remove === true) {
            this.updateSelectedChoice(remove);
        }
        this.updateHighlightedChoice();
    }

    execute() {
        if (this.selected_choice === undefined) {
            console.error('UNDEFINED QUICKSEARCH CHOICE', this.$input.val(), this.choices, this.visible_choices);
            return;
        }

        let value = this.$input.val();
        if (this.$input.val().startsWith(this.selected_choice.shortcut)) {
            value = value.substr(this.selected_choice.shortcut.length - 1);
        }
        let quicksearch = this;

        switch (this.selected_choice.type) {
            case TYPES.navigate:
                window.location = this.selected_choice.url;
                quicksearch.hide();
                break;
            case TYPES.event:
                if (this.selected_choice.event_value !== undefined) {
                    Pachno.trigger(this.selected_choice.event, this.selected_choice.event_value);
                } else {
                    Pachno.trigger(this.selected_choice.event, value);
                }
                quicksearch.hide();
                break;
            case TYPES.dynamic_choices:
                quicksearch.updateDynamicChoices(this.selected_choice.event, this.selected_choice.event_value);
                break;
            case TYPES.backdrop:
                UI.Backdrop.show(this.selected_choice.backdrop_url);
                quicksearch.hide();
                break;
        }
    }

    getHighlightedChoice() {
        if (this.highlighted_choice === undefined)
            return;

        return this.visible_choices[this.highlighted_choice];
    }

    selectHighlightedChoice() {
        const highlightedChoice = this.getHighlightedChoice();
        let changed = false;

        if (highlightedChoice !== undefined && (this.selected_choice === undefined || this.selected_choice.shortcut !== highlightedChoice.shortcut)) {
            const value = this.$input.val().trim();
            if (this.selected_choice !== undefined && value !== this.selected_choice.shortcut && value.startsWith(this.selected_choice.shortcut)) {
                this.$input.val(this.$input.val().substr(this.selected_choice.shortcut.length));
            }
            this.selected_choice = highlightedChoice;
            changed = true;
        }

        return changed;
    }

    selectHighlightedChoiceOrExecute() {
        const changed = this.selectHighlightedChoice(true);

        if ([TYPES.navigate, TYPES.event].includes(this.selected_choice.type) || !changed) {
            this.execute();
        } else {
            this.updateSelectedChoice();
        }
    }

    setupListeners() {
        const quicksearch = this;

        $body.on('keydown', function (event) {
            if (['INPUT', 'TEXTAREA'].indexOf(event.target.nodeName) !== -1) {
                return;
            }

            if (event.key === '/') {
                event.stopPropagation();
                event.preventDefault();
                quicksearch.show();
            }
        });

        $body.on('keydown', '.quicksearch-container', function (event) {
            if (!quicksearch.enabled)
                return;

            switch (event.key) {
                case 'Escape':
                    quicksearch.hide();
                    break;
                case 'ArrowUp':
                    quicksearch.navigatePreviousChoice();
                    event.preventDefault();
                    break;
                case 'ArrowDown':
                    quicksearch.navigateNextChoice();
                    event.preventDefault();
                    break;
                case 'Enter':
                    quicksearch.selectHighlightedChoiceOrExecute();
                    break;
            }
        });

        $body.on('keyup', '.quicksearch-container', function (event) {
            if (!quicksearch.enabled)
                return;

            console.log(quicksearch);
            switch (event.key) {
                case 'Escape':
                case 'ArrowUp':
                case 'ArrowDown':
                case 'Enter':
                    break;
                case 'Backspace':
                    quicksearch.updateHighlightedChoiceFromInput(true);
                    break;
                case ' ':
                    if (quicksearch.highlighted_choice !== undefined && quicksearch.$input.val().trim().startsWith(quicksearch.visible_choices[quicksearch.highlighted_choice].shortcut)) {
                        quicksearch.selectHighlightedChoice();
                        quicksearch.updateSelectedChoice();
                    }
                    break;
                default:
                    quicksearch.updateHighlightedChoiceFromInput();
                    console.log(event.key);
                    // case ''
            }
        });

        $body.off('click', '.trigger-quicksearch');
        $body.on('click', '.trigger-quicksearch', () => {
            Pachno.trigger(Pachno.EVENTS.quicksearchTrigger, { choices: this.default_choices });
        });

        Pachno.on(Pachno.EVENTS.quicksearchTrigger, function (Pachno, data) {
            quicksearch.show(data.default_value, data.choices);
        });
        Pachno.on(Pachno.EVENTS.quicksearchUpdateChoices, function (Pachno, choices) {
            quicksearch.highlighted_choice = undefined;
            quicksearch.visible_choices = choices;
            for (const index in quicksearch.visible_choices) {
                if (quicksearch.visible_choices.hasOwnProperty(index)) {
                    quicksearch.visible_choices[index].previous_choice = quicksearch.selected_choice;
                }
            }
            quicksearch.showChoices();
        });
        Pachno.on(Pachno.EVENTS.quicksearchAddDefaultChoice, (Pachno, choice) => {
            if (choice.choices !== undefined) {
                for (const index in choice.choices) {
                    if (choice.choices.hasOwnProperty(index)) {
                        choice.choices[index].previous_choice = choice;
                    }
                }
            }
            quicksearch.default_choices.push(choice);
        });
    }
}

export default Quicksearch;
window.Quicksearch = Quicksearch;

export const TYPES = {
    url: 'url',
    navigate: 'navigate',
    event: 'event',
    backdrop: 'trigger-backdrop',
    dynamic_choices: 'dynamic-choices'
};
