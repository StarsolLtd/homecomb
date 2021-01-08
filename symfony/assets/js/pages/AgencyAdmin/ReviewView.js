import React, {Fragment} from 'react';
import {Label, FormText, Button, Container} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import {AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import CommentForm from "../../components/CommentForm";

class ReviewView extends React.Component {
    constructor(props) {
        super(props);

        this.state = {
            commentPosted: false
        };

        this.setCommentPosted = this.setCommentPosted.bind(this);
    }

    setCommentPosted() {
        this.setState({commentPosted: true})
    }

    render() {
        return (
            <Container>
                <h1>Review</h1>

                {!this.state.commentPosted &&
                    <CommentForm
                        {...this.props}
                        onSuccess={this.setCommentPosted}
                        entityId={this.props.computedMatch.params.id}
                        entityName="Review"
                    />
                }
            </Container>
        );
    }
}

export default ReviewView;
