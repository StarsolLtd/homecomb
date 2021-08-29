import React from 'react';
import { Container } from 'reactstrap';
import FlashMessages from "../layout/FlashMessages";
import {addFlashMessage, fetchFlashMessages} from '../utils/FlashMessagesUtil.js'

export default class View extends React.Component {

    state = {
        flashMessages: [],
        flashMessagesFetching: false,
    };

    constructor(props) {
        super(props);

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
