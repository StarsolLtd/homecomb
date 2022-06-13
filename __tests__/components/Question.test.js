import React from 'react'
import { shallow } from 'enzyme'
import Question from '../../assets/js/components/Question'

it('renders without crashing', () => {
  shallow(<Question />)
})
