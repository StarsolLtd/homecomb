import React from 'react';
import { Container } from 'reactstrap';
import FlashMessages from "../layout/FlashMessages";
import {addFlashMessage, fetchFlashMessages} from '../utils/FlashMessagesUtil.js'

class View extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            flashMessages: [],
            flashMessagesFetching: false,
        };

        this.addFlashMessage = addFlashMessage.bind(this)
        this.fetchFlashMessages = fetchFlashMessages.bind(this)
    }

    componentDidMount() {
        this.fetchFlashMessages();
    }

    render() {
        const Content = this.props.content;
        return (
            <Container>
                <FlashMessages messages={this.state.flashMessages} />
                <Content
                    {...this.props}
                    addFlashMessage={this.addFlashMessage}
                    fetchFlashMessages={this.fetchFlashMessages}
                />
            </Container>
        );
    }
}

export default View;