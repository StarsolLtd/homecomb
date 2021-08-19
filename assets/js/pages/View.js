import React from 'react';
import { Container } from 'reactstrap';
import FlashMessages from "../layout/FlashMessages";
import FlashMessagesView from "./FlashMessagesView";

class View extends FlashMessagesView {
    constructor(props) {
        super(props);

        this.state = {
            flashMessages: [],
            flashMessagesFetching: false,
        };
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