import React, {Fragment} from 'react';
import {Col, Container, Progress, Row} from 'reactstrap';
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";
import Question from "../components/Question";
import SurveyCompletedThankYou from "../content/SurveyCompletedThankYou";

export default class Survey extends React.Component {

    state = {
        title: '',
        description: '',
        currentQuestion: 1,
        loaded: false,
    };

    render() {
        return (
            <Container>
                <DataLoader
                    url={'/api/s/' + this.props.match.params.slug}
                    loadComponentData={this.loadData}
                />
                {this.state.loaded &&
                    <div>
                        <Row>
                            <Col md="12" className="page-title">
                                <h1>
                                    {this.state.title}
                                </h1>
                                <p>
                                    {this.state.description}
                                </p>
                            </Col>
                        </Row>
                        <div className="bg-white rounded shadow-sm p-4 mb-4">
                            <Row>
                                <Col md="12">
                                    {this.state.questions.map(
                                        ({ id, type, content, help, highMeaning, lowMeaning, sortOrder, choices }) => (
                                            <Question
                                                {...this.props}
                                                key={id}
                                                questionId={id}
                                                type={type}
                                                content={content}
                                                choices={choices}
                                                help={help}
                                                highMeaning={highMeaning}
                                                lowMeaning={lowMeaning}
                                                sortOrder={sortOrder}
                                                totalQuestions={this.state.questions.length}
                                                visible={sortOrder === this.state.currentQuestion}
                                                back={this.back}
                                                forward={this.forward}
                                            />
                                        )
                                    )}
                                    {this.state.currentQuestion > this.state.questions.length &&
                                        <Fragment>
                                            <p>
                                                All questions answered
                                            </p>
                                            <Progress
                                                min={1}
                                                max={this.state.questions.length + 1}
                                                value={this.state.questions.length + 1}
                                                color="primary"
                                            />
                                            <hr />
                                            <SurveyCompletedThankYou />
                                        </Fragment>
                                    }
                                </Col>
                            </Row>
                        </div>
                    </div>
                }
            </Container>
        );
    }

    loadData = (data) => {
        this.setState({
            title: data.title,
            description: data.description,
            questions: data.questions,
            loaded: true,
        });

        document.title = this.state.title + ' | ' + Constants.SITE_NAME;
    }

    back = (number) => {
        this.setState({
            currentQuestion: number - 1,
        });
    }

    forward = (number) => {
        this.setState({
            currentQuestion: number + 1,
        });
    }
}
