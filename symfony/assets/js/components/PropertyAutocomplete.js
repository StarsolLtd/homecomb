import React, {Fragment} from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import 'jquery-ui-bundle/jquery-ui.css';
import {Input, InputGroup} from "reactstrap";
import {Redirect} from "react-router-dom";
import { faSearch } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

import '../../styles/property-autocomplete.scss';

class PropertyAutocomplete extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            inputId: this.props.inputId || 'propertySearch',
            redirectToUrl: null,
        };

        this.redirectToPropertyView = this.redirectToPropertyView.bind(this);
    }

    componentDidMount(){
        $('#' + this.state.inputId).autocomplete({
            source: this.props.source || '/api/property/suggest-property',
            minLength: this.props.minLength || 3,
            select: this.redirectToPropertyView
        });
    }

    render(){
        return (
            <Fragment>
                <InputGroup className="property-autocomplete-input-group">
                    <Input
                        type="text"
                        id={this.state.inputId || 'propertySearch'}
                        placeholder={this.props.placeholder || 'Start typing an address... e.g. 249 Victoria Road'}
                        className="property-autocomplete"
                    />
                    <span className="input-group-append">
                        <button className="btn border-left-0" type="button">
                            <FontAwesomeIcon icon={faSearch} />
                        </button>
                    </span>
                </InputGroup>
                {this.state.redirectToUrl &&
                <Redirect to={this.state.redirectToUrl} />
                }
            </Fragment>
        )
    }

    redirectToPropertyView(event, ui){
        const vendorPropertyId = ui.item.id;
        fetch('/api/property/lookup-slug-from-vendor-id?vendorPropertyId=' + vendorPropertyId,)
            .then(response => response.json())
            .then(data => {
                this.setState({redirectToUrl: '/property/' + data.slug})
            });
    }
}

export default PropertyAutocomplete;