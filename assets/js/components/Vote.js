import React from 'react'

import { Button } from 'reactstrap'

import Constants from '../Constants'
import { faThumbsUp } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import '../../styles/vote.scss'
import LoadingSpinner from './LoadingSpinner'
import LoginOrRegister from '../modals/LoginOrRegister'

export default class Vote extends React.Component {
  state = {
    hasVoted: false,
    isSubmitting: false,
    positiveVotes: this.props.positiveVotes,
    showLoginModal: false
  }

  hideLoginModal = () => {
    this.setState({ showLoginModal: false })
  }

  showLoginModal = () => {
    this.setState({ showLoginModal: true })
  }

  render () {
    let buttonClassName = 'vote-button btn-light ' + this.props.className
    if (this.state.hasVoted) {
      buttonClassName += ' has-voted'
    }
    if (this.state.showLoginModal) {
      return <LoginOrRegister showModal={true} hideLoginModal={this.hideLoginModal} />
    }
    return (
      <Button onClick={this.handleVote} className={buttonClassName}>
        <FontAwesomeIcon icon={faThumbsUp} className="text-primary"/> {this.props.positiveTerm}
        {this.state.positiveVotes > 0 &&
          <>
            {' '}
            <span className="positive-votes">{this.state.positiveVotes}</span>
          </>
        }
        {this.state.isSubmitting &&
          <>
            {' '}
            <LoadingSpinner className="loading-spinner-small"/>
          </>
        }
      </Button>
    )
  }

  handleVote = () => {
    if (this.state.hasVoted) {
      return
    }

    this.setState({ isSubmitting: true })
    const payload = {
      entityId: this.props.entityId,
      entityName: this.props.entityName,
      positive: true,
      captchaToken: null
    }
    const component = this
    /* eslint-disable-next-line no-undef */
    grecaptcha.ready(function () {
      /* eslint-disable-next-line no-undef */
      grecaptcha.execute(Constants.GOOGLE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then(function (captchaToken) {
        payload.captchaToken = captchaToken
        fetch('/api/vote', { method: 'POST', body: JSON.stringify(payload) })
          .then(
            response => {
              component.setState({ isSubmitting: false })
              if (!response.ok) {
                if (response.status === 401) {
                  component.showLoginModal()
                }
                /* eslint-disable-next-line prefer-promise-reject-errors */
                return Promise.reject('Error: ' + response.status)
              }
              component.setState({ hasVoted: true })

              return response.json()
            }
          )
          .then((data) => {
            component.setState({ positiveVotes: data.positiveVotes })
          })
          .catch(err => console.error('Error:', err))
      })
    })
  }
}
