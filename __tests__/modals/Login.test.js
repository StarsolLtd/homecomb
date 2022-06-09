import React from 'react'
import { shallow } from 'enzyme'
import Login from '../../assets/js/modals/Login'

it('renders without crashing', () => {
  shallow(<Login />)
})
