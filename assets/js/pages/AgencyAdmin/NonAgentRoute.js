import React, { Fragment } from 'react'
import { Redirect } from 'react-router-dom'

export default class NonAgentRoute extends React.Component {
  render () {
    const Component = this.props.render
    return (
      <Fragment>
        {!this.props.isAgencyAdmin &&
          <Component />
        }
        {this.props.isAgencyAdmin &&
          <Redirect to={{ pathname: '/verified/dashboard', state: { from: this.props.location } }} />
        }
      </Fragment>
    )
  }
}
