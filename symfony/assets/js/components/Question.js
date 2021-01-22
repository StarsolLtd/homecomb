import React, {Fragment} from 'react';
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
        this.back = this.back.bind(this);
        this.forward = this.forward.bind(this);
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
            <Fragment>
                {this.props.visible &&
                    <div className="question" id={"question" + this.props.sortOrder}>
                        <p>
                            Question {this.props.sortOrder} of {this.props.totalQuestions}
                        </p>
                        <Label for="content"><h2>{this.props.content}</h2></Label>
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
                            <Button className="question-form-submit mb-3" color="primary" size="lg">
                                Submit {this.props.totalQuestions === this.props.sortOrder && ' and Complete'}
                            </Button>
                        </AvForm>
                        {this.props.sortOrder > 1 &&
                            <a className="question-back" onClick={this.back}>Back</a>
                        }
                        <a className="question-skip" onClick={this.forward}>Skip {this.props.totalQuestions === this.props.sortOrder && ' and Complete'}</a>
                    </div>
                }
            </Fragment>
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
                this.forward();
            })
            .catch(err => console.error("Error:", err));
    }

    back() {
        this.props.back(this.props.sortOrder);
    }

    forward() {
        this.props.forward(this.props.sortOrder);
    }
}

export default Question;