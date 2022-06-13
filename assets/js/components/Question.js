import React, { useRef, useState } from 'react'
import PropTypes from 'prop-types'
import { AvFeedback, AvForm, AvGroup, AvInput, AvRadio, AvRadioGroup } from 'availity-reactstrap-validation'
import { Button, FormText, Label, Progress } from 'reactstrap'

import '../../styles/question.scss'
import Scale from './Scale'

const Question = (props) => {
  const [content, setContent] = useState('')
  const [rating, setRating] = useState(null)

  const questionForm = useRef(null)

  const handleChange = (event) => {
    const target = event.target
    const value = target.type === 'checkbox' ? target.checked : target.value

    switch (target.name) {
      case 'content':
        setContent(value)
        break
    }
  }

  const handleRatingChange = (value) => {
    setRating(value)
  }

  const handleValidSubmit = (event, values) => {
    const payload = {
      questionId: props.questionId,
      choiceId: values.choiceId,
      content: values.content,
      rating
    }

    fetch('/api/s/answer', {
      method: 'POST',
      body: JSON.stringify(payload)
    })
      .then(
        response => {
          if (!response.ok) {
            /* eslint-disable-next-line prefer-promise-reject-errors */
            return Promise.reject('Error: ' + response.status)
          }
          return response.json()
        }
      )
      .then((data) => {
        forward()
      })
      .catch(err => console.error('Error:', err))
  }

  const back = () => {
    props.back(props.sortOrder)
  }

  const forward = () => {
    props.forward(props.sortOrder)
  }

  return (
    <>
      {props.visible &&
        <div className="question" id={'question' + props.sortOrder}>
          <p>
            Question {props.sortOrder} of {props.totalQuestions}
          </p>
          <Progress
            min={1}
            max={props.totalQuestions + 1}
            value={props.sortOrder}
            color="primary"
          />
          <hr />
          <Label for="content"><h2>{props.content}</h2></Label>
          <AvForm className="question-form" onValidSubmit={handleValidSubmit} ref={questionForm}>
            {props.type === 'free' &&
              <AvGroup>
                <AvInput
                  type="textarea"
                  name="content"
                  value={content}
                  placeholder="Enter your answer"
                  required
                  onChange={handleChange}
                />
                <AvFeedback>Please enter your answer.</AvFeedback>
                <FormText>
                  {props.help}
                </FormText>
              </AvGroup>
            }
            {props.type === 'choice' &&
              <AvRadioGroup inline name="choiceId" required errorMessage="Please choose an answer">
                {props.choices.map(
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
            {props.type === 'scale5' &&
              <Scale
                {...props}
                max={5}
                rating={rating}
                handleRatingChange={handleRatingChange}
              />
            }
            <Button className="question-form-submit mb-3" color="primary" size="lg">
              Submit {props.totalQuestions === props.sortOrder && ' and Complete'}
            </Button>
          </AvForm>
          {props.sortOrder > 1 &&
            <a className="question-back" onClick={back}>Back</a>
          }
          <a className="question-skip" onClick={forward}>Skip {props.totalQuestions === props.sortOrder && ' and Complete'}</a>
        </div>
      }
    </>
  )
}

Question.defaultProps = {
  content: '',
  rating: null,
  isFormSubmitting: false
}

Question.propTypes = {
  visible: PropTypes.bool,
  content: PropTypes.string,
  type: PropTypes.string,
  totalQuestions: PropTypes.number,
  sortOrder: PropTypes.number,
  back: PropTypes.func,
  forward: PropTypes.func
}

export default Question
