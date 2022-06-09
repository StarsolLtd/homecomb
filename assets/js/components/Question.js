import React from 'react'
import { AvFeedback, AvForm, AvGroup, AvInput, AvRadio, AvRadioGroup } from 'availity-reactstrap-validation'
import { Button, FormText, Label, Progress } from 'reactstrap'

import '../../styles/question.scss'
import Scale from './Scale'

export default class Question extends React.Component {
  state = {
    content: '',
    rating: null,
    isFormSubmitting: false
  }

  handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value
    const name = target.name

    this.setState({
      [name]: value
    })
  }

  handleRatingChange = (value) => {
    this.setState({ rating: value })
  }

  render () {
    return (
      <>
        {this.props.visible &&
          <div className="question" id={'question' + this.props.sortOrder}>
            <p>
              Question {this.props.sortOrder} of {this.props.totalQuestions}
            </p>
            <Progress
              min={1}
              max={this.props.totalQuestions + 1}
              value={this.props.sortOrder}
              color="primary"
            />
            <hr />
            <Label for="content"><h2>{this.props.content}</h2></Label>
            <AvForm className="question-form" onValidSubmit={this.handleValidSubmit} ref={c => (this.form = c)}>
              {this.props.type === 'free' &&
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
              }
              {this.props.type === 'choice' &&
                <AvRadioGroup inline name="choiceId" required errorMessage="Please choose an answer">
                  {this.props.choices.map(
                    ({ id, name }) => (
                      <AvRadio
                        key={id}
                        label={name}
                        value={id}
                      />
                    )
                  )}
                </AvRadioGroup>
              }
              {this.props.type === 'scale5' &&
                <Scale
                  {...this.props}
                  max={5}
                  rating={this.state.rating}
                  handleRatingChange={this.handleRatingChange}
                />
              }
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
      </>
    )
  }

  handleValidSubmit = (event, values) => {
    this.setState({ isFormSubmitting: true })
    const payload = {
      questionId: this.props.questionId,
      choiceId: values.choiceId,
      content: values.content,
      rating: this.state.rating
    }

    fetch('/api/s/answer', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
      .then(
        response => {
          this.setState({ isFormSubmitting: false })
          if (!response.ok) {
            /* eslint-disable-next-line prefer-promise-reject-errors */
            return Promise.reject('Error: ' + response.status)
          }
          return response.json()
        }
      )
      .then((data) => {
        this.forward()
      })
      .catch(err => console.error('Error:', err))
  }

  back = () => {
    this.props.back(this.props.sortOrder)
  }

  forward = () => {
    this.props.forward(this.props.sortOrder)
  }
}
