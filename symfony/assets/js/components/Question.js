import React from 'react';
import {AvFeedback, AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import {Button, FormText, Label} from "reactstrap";

import '../../styles/question.scss';

class Question extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            content: '',
            isFormSubmitting: false,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleValidSubmit = this.handleValidSubmit.bind(this);
    }

    handleChange(event) {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({
            [name]: value
        });
    }

    render() {
        return (
            <div className="question">
                <Label for="content">{this.props.content}</Label>
                <AvForm className="question-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
                    <AvGroup>
                        <AvInput
                            type="textarea"
                            name="content"
                            value={this.state.content}
                            placeholder="Enter your answer"
                            required
                            onChange={this.handleChange}
                        />
                        <AvFeedback>Please enter your answer.</AvFeedback>
                        <FormText>
                            {this.props.help}
                        </FormText>
                    </AvGroup>
                    <Button className="question-form" color="primary">
                        Submit
                    </Button>
                </AvForm>
            </div>
        );
    }

    handleValidSubmit() {
        this.setState({isFormSubmitting: true});
        let payload = {
            questionId: this.props.questionId,
            content: this.state.content
        };

        fetch('/api/s/answer', {
            method: 'POST',
            body: JSON.stringify(payload),
        })
            .then(
                response => {
                    this.setState({isFormSubmitting: false});
                    if (!response.ok) {
                        return Promise.reject('Error: ' + response.status)
                    }
                    return response.json()
                }
            )
            .then((data) => {
                this.clearForm();
            })
            .catch(err => console.error("Error:", err));
    }

    clearForm() {
        this.form && this.form.reset();
    }
}

export default Question;