import React from 'react';
import { Container } from 'reactstrap';
import FlashMessages from "../layout/FlashMessages";

class View extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            flashMessages: [],
            flashMessagesFetching: false,
        };

        this.addFlashMessage = this.addFlashMessage.bind(this);
        this.fetchFlashMessages = this.fetchFlashMessages.bind(this);
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
                    addFlashMessage={this.addFlashMessage}
                    fetchFlashMessages={this.fetchFlashMessages}
                    submit={this.submit}
                    {...this.props}
                />
            </Container>
        );
    }

    addFlashMessage(context, content) {
        this.setState({ flashMessages: [...this.state.flashMessages, {key: Date.now(), context, content}] })
    }

    fetchFlashMessages(scrollTo=true) {
        fetch('/api/session/flash')
            .then(
                response => {
                    this.setState({flashMessagesFetching: false})
                    if (!response.ok) {
                        return Promise.reject('Error: ' + response.status)
                    }
                    return response.json()
                }
            )
            .then(data => {
                data.messages.forEach(message => this.addFlashMessage(message.type, message.message));
                if (scrollTo) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
    }
}

export default View;