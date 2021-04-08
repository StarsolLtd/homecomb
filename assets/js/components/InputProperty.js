import React from 'react';
import $ from 'jquery';
import 'jquery-ui-bundle';
import 'jquery-ui-bundle/jquery-ui.css';
import {Input} from "reactstrap";

class InputProperty extends React.Component {

    constructor(props) {
        super(props);
        this.handleSelect = this.handleSelect.bind(this);
        this.setPropertySlugState = this.props.setPropertySlugState;
    }

    componentDidMount(){
        $('#' + this.props.inputId).autocomplete({
            source: this.props.source,
            minLength: this.props.minLength || 3,
            select: this.handleSelect
        });
    }

    render(){
        return (
            <Input
                type="text"
                id={this.props.inputId}
                placeholder={this.props.placeholder || 'Start typing'}
                autoComplete="off"
            />
        )
    }

    handleSelect(event, ui){
        const vendorPropertyId = ui.item.id;
        fetch('/api/property/lookup-slug-from-vendor-id?vendorPropertyId=' + vendorPropertyId,)
            .then(response => response.json())
            .then(data => {
                this.setPropertySlugState(data.slug);
            });
    }
}

export default InputProperty;