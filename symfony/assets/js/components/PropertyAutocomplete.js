import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';

class PropertyAutocomplete extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            inputId: this.props.inputId,
            source: this.props.source,
            placeholder: this.props.placeholder || 'Start typing',
            minLength: this.props.minLength || 3,
        };
    }

    componentDidMount(){
        $('#' + this.state.inputId).autocomplete({
            source: this.state.source,
            minLength: this.state.minLength,
            select: this.redirectToPropertyView
        });
    }

    render(){
        return (
            <input type="text" className="form-control" id={this.state.inputId} placeholder={this.state.placeholder} />
        )
    }

    redirectToPropertyView(event, ui){
        $.get('/api/property/lookup-slug-from-vendor-id?vendorPropertyId=' + ui.item.id, function( data ) {
            location.href = '/property/' + data.slug;
        });
    }
}

export default PropertyAutocomplete;