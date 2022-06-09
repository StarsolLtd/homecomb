import React from 'react'
import { shallow } from 'enzyme'
import LoginOrRegister from '../../assets/js/modals/LoginOrRegister'

it('renders without crashing', () => {
  shallow(<LoginOrRegister />)
})
