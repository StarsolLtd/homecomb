import React from 'react';
import { Container } from 'reactstrap';
import LoadingOverlay from "react-loading-overlay";
import Loader from "react-loaders";
import FlashMessages from "../../layout/FlashMessages";
import { Redirect } from 'react-router-dom'
import Constants from "../../Constants";

class View extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            isFormSubmitting: false,
            flashMessages: [],
            redirectToUrl: null
        };

        this.addFlashMessage = this.addFlashMessage.bind(this);
        this.submit = this.submit.bind(this);
    }

    render() {
        const Content = this.props.content;

        return (
            <Container>
                <FlashMessages messages={this.state.flashMessages} />
                <LoadingOverlay
                    active={this.state.isFormSubmitting}
                    styles={{
                        overlay: (base) => ({
                            ...base,
                            background: "#fff",
                            opacity: 0.5,
                        }),
                    }}
                    spinner={<Loader active type='ball-triangle-path' />}
                >
                    <Content
                        addFlashMessage={this.addFlashMessage}
                        submit={this.submit}
                        {...this.props}
                    />
                </LoadingOverlay>
                {this.state.redirectToUrl &&
                    <Redirect to={this.state.redirectToUrl} />
                }
            </Container>
        );
    }

    addFlashMessage(context, content) {
        this.setState({ flashMessages: [...this.state.flashMessages, {key: Date.now(), context, content}] })
    }

    submit(payload, url, method, successMessage, successRedirectUrl='') {
        this.setState({isFormSubmitting: true});

        let component = this;
        grecaptcha.ready(function() {
            grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, {action: 'submit'}).then(function(captchaToken) {
                payload.captchaToken = captchaToken;
                fetch(url, {method: method, body: JSON.stringify(payload)})
                    .then((response) => {
                        component.setState({isFormSubmitting: false});
                        if (!response.ok) throw new Error(response.status);
                        else return response.json();
                    })
                    .then((data) => {
                        component.addFlashMessage('success', successMessage)
                        if (successRedirectUrl) {
                            component.setState({redirectToUrl: successRedirectUrl});
                        }
                    })
                    .catch(err => console.error("Error:", err));
            });
        });
    }
}

export default View;