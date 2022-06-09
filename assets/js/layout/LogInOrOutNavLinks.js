import React, { Component } from 'react'
import Login from '../modals/Login'

export default class LogInOurOutNavLinks extends Component {
  state = {
    showLoginModal: false
  }

  hideLoginModal = () => {
    this.setState({ showLoginModal: false })
  }

  showLoginModal = () => {
    this.setState({ showLoginModal: true })
  }

  render () {
    return (
      <>
        {this.state.showLoginModal &&
          <Login hideLoginModal={this.hideLoginModal} />
        }

        {this.props.user &&
          <li><a href="/logout" className={this.props.className}>Log Out</a></li>
        }
        {!this.props.user &&
          <>
            <li><a onClick={this.showLoginModal} className={this.props.className}>Log In</a></li>
            <li><a href="/register" className={this.props.className + ' register-link'}>Register</a></li>
          </>
        }
      </>
    )
  }
}
