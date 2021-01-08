import React, {Fragment} from 'react';
import {Label, FormText, Button, Container} from 'reactstrap';
import DataLoader from "../../components/DataLoader";
import {AvForm, AvGroup, AvInput} from "availity-reactstrap-validation";
import CommentForm from "../../components/CommentForm";

class ReviewView extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Container>
                <CommentForm {...this.props} entityId={this.props.computedMatch.params.id} entityName="Review" />
            </Container>
        );
    }
}

export default ReviewView;
