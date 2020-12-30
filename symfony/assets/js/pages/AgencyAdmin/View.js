import React from 'react';
import {Label, FormText, Button, Container} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import {AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import Constants from "../../Constants";
import FlashMessages from "../../layout/FlashMessages";
import UpdateAgency from "./UpdateAgency";

class View extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            flashMessages: []
        };

        this.addFlashMessage = this.addFlashMessage.bind(this);
    }

    render() {
        const Content = this.props.content;

        return (
            <Container>
                <FlashMessages messages={this.state.flashMessages} />
                <Content
                    addFlashMessage={this.addFlashMessage}
                />
            </Container>
        );
    }

    addFlashMessage(context, content) {
        this.setState({ flashMessages: [...this.state.flashMessages, {key: Date.now(), context, content}] })
    }
}

export default View;