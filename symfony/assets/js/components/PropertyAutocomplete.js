import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import 'jquery-ui-bundle/jquery-ui.css';
import {Input} from "reactstrap";

class PropertyAutocomplete extends React.Component {
    
    componentDidMount(){
        $('#' + this.props.inputId).autocomplete({
            source: this.props.source,
            minLength: this.props.minLength || 3,
            select: this.redirectToPropertyView
        });
    }

    render(){
        return (
            <Input type="text" id={this.props.inputId} placeholder={this.props.placeholder || 'Start typing'} />
        )
    }

    redirectToPropertyView(event, ui){
        $.get('/api/property/lookup-slug-from-vendor-id?vendorPropertyId=' + ui.item.id, function( data ) {
            location.href = '/property/' + data.slug;
        });
    }
}

export default PropertyAutocomplete;