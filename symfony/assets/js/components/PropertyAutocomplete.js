import React, {Fragment} from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import 'jquery-ui-bundle/jquery-ui.css';
import {Input} from "reactstrap";
import {Redirect} from "react-router-dom";

class PropertyAutocomplete extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            redirectToUrl: null
        };

        this.redirectToPropertyView = this.redirectToPropertyView.bind(this);
    }

    componentDidMount(){
        $('#' + this.props.inputId).autocomplete({
            source: this.props.source,
            minLength: this.props.minLength || 3,
            select: this.redirectToPropertyView
        });
    }

    render(){
        return (
            <Fragment>
                <Input type="text" id={this.props.inputId} placeholder={this.props.placeholder || 'Start typing'} />
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