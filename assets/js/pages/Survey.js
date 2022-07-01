import React, { useState } from 'react'
import { Col, Container, Progress, Row } from 'reactstrap'
import DataLoader from '../components/DataLoader'
import Constants from '../Constants'
import Question from '../components/Question'
import SurveyCompletedThankYou from '../content/SurveyCompletedThankYou'
import PropTypes from 'prop-types'

const Survey = (props) => {
  const [title, setTitle] = useState('')
  const [description, setDescription] = useState('')
  const [currentQuestion, setCurrentQuestion] = useState(1)
  const [questions, setQuestions] = useState([])
  const [loaded, setLoaded] = useState(false)

  const loadData = (data) => {
    setTitle(data.title)
    setDescription(data.description)
    setQuestions(data.questions)
    setLoaded(true)

    document.title = data.title + ' | ' + Constants.SITE_NAME
  }

  const back = (number) => {
    setCurrentQuestion(number - 1)
  }

  const forward = (number) => {
    setCurrentQuestion(number + 1)
  }

  return (
    <Container>
      <DataLoader
        url={'/api/s/' + props.match.params.slug}
        loadComponentData={loadData}
      />
      {loaded &&
        <div>
          <Row>
            <Col md="12" className="page-title">
              <h1>
                {title}
              </h1>
              <p>
                {description}
              </p>
            </Col>
          </Row>
          <div className="bg-white rounded shadow-sm p-4 mb-4">
            <Row>
              <Col md="12">
                {questions.map(
                  ({ id, type, content, help, highMeaning, lowMeaning, sortOrder, choices }) => (
                    <Question
                      {...props}
                      key={id}
                      questionId={id}
                      type={type}
                      content={content}
                      choices={choices}
                      help={help}
                      highMeaning={highMeaning}
                      lowMeaning={lowMeaning}
                      sortOrder={sortOrder}
                      totalQuestions={questions.length}
                      visible={sortOrder === currentQuestion}
                      back={back}
                      forward={forward}
                    />
                  )
                )}
                {currentQuestion > questions.length &&
                  <>
                    <p>
                      All questions answered
                    </p>
                    <Progress
                      min={1}
                      max={questions.length + 1}
                      value={questions.length + 1}
                      color="primary"
                    />
                    <hr />
                    <SurveyCompletedThankYou />
                  </>
                }
              </Col>
            </Row>
          </div>
        </div>
      }
    </Container>
  )
}

Survey.propTypes = {
  match: PropTypes.shape({
    params: PropTypes.shape({
      slug: PropTypes.string
    })
  })
}

export default Survey
