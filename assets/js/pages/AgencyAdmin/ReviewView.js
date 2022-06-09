import React from 'react'
import { Container } from 'reactstrap'
import DataLoader from '../../components/DataLoader'
import CommentForm from '../../components/CommentForm'
import Review from '../../components/Review'

export default class ReviewView extends React.Component {
  state = {
    loaded: false,
    commentPosted: false
  }

  setCommentPosted = () => {
    this.setState({ commentPosted: true })
  }

  render () {
    return (
      <Container>
        <h1>Review</h1>

        <DataLoader
          url={'/api/review/' + this.props.computedMatch.params.id}
          loadComponentData={this.loadData}
        />
        {this.state.loaded &&
          <div className="bg-white rounded shadow-sm p-4 mb-4">
            <Review
              id={this.state.id}
              author={this.state.author}
              title={this.state.title}
              content={this.state.content}
              property={this.state.property}
              branch={this.state.branch}
              agency={this.state.agency}
              stars={this.state.stars}
              createdAt={this.state.createdAt}
              showOptions={false}
            />
          </div>
        }
        {!this.state.commentPosted &&
          <div className="bg-white rounded shadow-sm p-4 mb-4">
            <CommentForm
              {...this.props}
              onSuccess={this.setCommentPosted}
              entityId={this.props.computedMatch.params.id}
              entityName="Review"
            />
          </div>
        }
      </Container>
    )
  }

  loadData = (data) => {
    this.setState({
      id: data.id,
      author: data.author,
      title: data.title,
      content: data.content,
      property: data.property,
      branch: data.branch,
      agency: data.agency,
      stars: data.stars,
      createdAt: data.createdAt,
      loaded: true
    })
  }
}
