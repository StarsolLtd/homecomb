import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';

class InputProperty extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            inputId: this.props.inputId,
            source: this.props.source,
            placeholder: this.props.placeholder || 'Start typing',
            minLength: this.props.minLength || 3,
            propertySlug: '',
        };
        this.handleSelect = this.handleSelect.bind(this);
        this.setPropertySlugState = this.props.setPropertySlugState;
    }

    componentDidMount(){
        $('#' + this.state.inputId).autocomplete({
            source: this.state.source,
            minLength: this.state.minLength,
            select: this.handleSelect
        });
    }

    render(){
        return (
            <input type="text" className="form-control" id={this.state.inputId} placeholder={this.state.placeholder} />
        )
    }

    handleSelect(event, ui){
        const vendorPropertyId = ui.item.id;
        fetch(
            '/api/property/lookup-slug-from-vendor-id?vendorPropertyId=' + vendorPropertyId,
            {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }
        )
            .then(response => response.json())
            .then(data => {
                this.setPropertySlugState(data.slug);
            });
    }
}

export default InputProperty;