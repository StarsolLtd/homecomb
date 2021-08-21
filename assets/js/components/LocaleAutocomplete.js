import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import 'jquery-ui-bundle/jquery-ui.css';
import {Input, InputGroup} from "reactstrap";
import { faSearch } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import '../../styles/autocomplete.scss';

class LocaleAutocomplete extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            inputId: this.props.inputId || 'localeSearch'
        };

        this.redirectToLocaleView = this.redirectToLocaleView.bind(this);
    }

    componentDidMount(){
        $('#' + this.state.inputId).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '/api/locale/suggest-locale?q=' + request.term,
                }).done(function(data) {
                    if (data.locales.length >= 1) {
                        response($.map(data.locales, function(item) {
                            return {
                                value: item.name,
                                slug: item.slug
                            };
                        }));
                    } else {
                        response([
                            {
                                value: 'No results found.',
                                id: '-1'
                            }
                        ]);
                    }
                });
            },
            minLength: this.props.minLength || 3,
            select: this.redirectToLocaleView
        });
    }

    render(){
        return (
            <div className={this.props.className}>
                <InputGroup className="autocomplete-input-group">
                    {this.props.prependSearchIcon &&
                        <span className="input-group-prepend">
                            <button className="btn btn-no-action border-right-0" type="button">
                                <FontAwesomeIcon icon={faSearch} />
                            </button>
                        </span>
                    }
                    <Input
                        type="text"
                        id={this.state.inputId}
                        placeholder={this.props.placeholder || 'Start typing... e.g. Cambridge'}
                        className="locale-autocomplete"
                    />
                    {this.props.appendSearchIcon &&
                        <span className="input-group-append">
                            <button className="btn btn-no-action border-left-0" type="button">
                                <FontAwesomeIcon icon={faSearch} />
                            </button>
                        </span>
                    }
                </InputGroup>
            </div>
        )
    }

    redirectToLocaleView(event, ui)
    {
        if (ui.item.slug) {
            window.location.href = '/l/' + ui.item.slug + '#';
        }
        // Otherwise, do nothing
    }
}

export default LocaleAutocomplete;