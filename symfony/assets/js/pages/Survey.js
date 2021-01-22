import React from 'react';
import {Col, Container, Row} from 'reactstrap';
import DataLoader from "../components/DataLoader";
import Constants from "../Constants";
import Question from "../components/Question";

class Survey extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            title: '',
            description: '',
            currentQuestion: 1,
            loaded: false,
        };

        this.loadData = this.loadData.bind(this);
        this.back = this.back.bind(this);
        this.forward = this.forward.bind(this);
    }

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
                                <Col xs="12" md="8">
                                    {this.state.questions.map(
                                        ({ id, type, content, help, highMeaning, lowMeaning, sortOrder }) => (
                                            <Question
                                                {...this.props}
                                                key={id}
                                                questionId={id}
                                                type={type}
                                                content={content}
                                                help={help}
                                                highMeaning={highMeaning}
                                                lowMeaning={lowMeaning}
                                                sortOrder={sortOrder}
                                                visible={sortOrder === this.state.currentQuestion}
                                                back={this.back}
                                                forward={this.forward}
                                            />
                                        )
                                    )}

                                </Col>
                            </Row>
                        </div>
                    </div>
                }
            </Container>
        );
    }

    loadData(data) {
        this.setState({
            title: data.title,
            description: data.description,
            questions: data.questions,
            loaded: true,
        });

        document.title = this.state.title + ' | ' + Constants.SITE_NAME;
    }

    back(number) {
        this.setState({
            currentQuestion: number - 1,
        });
    }

    forward(number) {
        this.setState({
            currentQuestion: number + 1,
        });
    }
}

export default Survey;
